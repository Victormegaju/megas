<?php
// API Controller for chat and other API endpoints
class ApiController {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }
    
    public function handle($uri, $method) {
        header('Content-Type: application/json');
        
        if ($uri === '/api/chat' && $method === 'POST') {
            $this->chat();
        } elseif ($uri === '/api/chat/history' && $method === 'GET') {
            $this->getChatHistory();
        } elseif ($uri === '/api/chat/clear' && $method === 'POST') {
            $this->clearChatHistory();
        } elseif ($uri === '/api/change-password' && $method === 'POST') {
            $this->changePassword();
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
    }
    
    private function chat() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Message is required']);
            return;
        }
        
        $message = $data['message'];
        $image = $data['image'] ?? null; // Base64 encoded image
        
        // Save user message to history
        $this->saveChatMessage('user', $message, !empty($image));
        
        // Get Gemini API settings
        $settings = new Settings($this->db);
        $apiKey = $settings->getGeminiApiKey();
        $model = $settings->getGeminiModel();
        
        if (empty($apiKey)) {
            http_response_code(500);
            echo json_encode(['error' => 'Gemini API not configured']);
            return;
        }
        
        try {
            // Call Gemini API
            $response = $this->callGeminiAPI($apiKey, $model, $message, $image);
            
            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }
            
            $assistantMessage = $response['text'] ?? 'Desculpe, não consegui processar sua mensagem.';
            $responseImage = $response['image'] ?? null;
            
            // Save assistant response to history
            $this->saveChatMessage('assistant', $assistantMessage, !empty($responseImage));
            
            echo json_encode([
                'success' => true,
                'message' => $assistantMessage,
                'image' => $responseImage
            ]);
            
        } catch (Exception $e) {
            error_log('Gemini API Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao processar mensagem: ' . $e->getMessage()]);
        }
    }
    
    private function callGeminiAPI($apiKey, $model, $message, $image = null) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        
        $parts = [
            ['text' => $message]
        ];
        
        // Add image if provided
        if ($image) {
            // Extract mime type and data from base64
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $image, $matches)) {
                $mimeType = 'image/' . $matches[1];
                $imageData = $matches[2];
                
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $imageData
                    ]
                ];
            }
        }
        
        $payload = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            throw new Exception($error['error']['message'] ?? 'API request failed');
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Invalid API response format');
        }
        
        return [
            'text' => $result['candidates'][0]['content']['parts'][0]['text']
        ];
    }
    
    private function saveChatMessage($type, $content, $hasImage) {
        $sql = 'INSERT INTO chat_history (user_id, message_type, content, has_image) VALUES (?, ?, ?, ?)';
        $this->db->execute($sql, [
            $this->user->id,
            $type,
            $content,
            $hasImage ? 1 : 0
        ]);
    }
    
    private function getChatHistory() {
        $limit = $_GET['limit'] ?? 50;
        $sql = 'SELECT message_type, content, has_image, created_at 
                FROM chat_history 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?';
        
        $messages = $this->db->fetchAll($sql, [$this->user->id, (int)$limit]);
        
        // Reverse to show oldest first
        $messages = array_reverse($messages);
        
        echo json_encode(['messages' => $messages]);
    }
    
    private function clearChatHistory() {
        $sql = 'DELETE FROM chat_history WHERE user_id = ?';
        $this->db->execute($sql, [$this->user->id]);
        
        echo json_encode(['success' => true]);
    }
    
    private function changePassword() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios não preenchidos']);
            return;
        }
        
        // Verify current password
        $userData = $this->db->fetchOne('SELECT password_hash FROM users WHERE id = ?', [$this->user->id]);
        
        if (!password_verify($currentPassword, $userData['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Senha atual incorreta']);
            return;
        }
        
        // Update password
        $this->user->updatePassword($newPassword);
        
        echo json_encode(['success' => true]);
    }
}
