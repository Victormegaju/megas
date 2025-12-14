<?php
// Mercado Pago Webhook Handler
class WebhookController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function handleMercadoPago() {
        // Log the webhook request
        $input = file_get_contents('php://input');
        error_log('Webhook received: ' . $input);
        
        header('Content-Type: application/json');
        
        // Parse webhook data
        $data = json_decode($input, true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }
        
        // Get payment ID from webhook
        $paymentId = $data['data']['id'] ?? null;
        $type = $data['type'] ?? null;
        
        if ($type !== 'payment' || !$paymentId) {
            http_response_code(200);
            echo json_encode(['status' => 'ignored']);
            return;
        }
        
        try {
            // Get payment details from Mercado Pago
            $settings = new Settings($this->db);
            $accessToken = $settings->getMercadoPagoAccessToken();
            
            if (empty($accessToken)) {
                throw new Exception('Access token not configured');
            }
            
            $paymentData = $this->getMercadoPagoPayment($accessToken, $paymentId);
            
            // Find payment in database
            $payment = $this->db->fetchOne('SELECT * FROM payments WHERE external_id = ?', [$paymentId]);
            
            if (!$payment) {
                error_log('Payment not found in database: ' . $paymentId);
                http_response_code(200);
                echo json_encode(['status' => 'payment_not_found']);
                return;
            }
            
            // Check if already processed
            if ($payment['status'] === 'approved') {
                http_response_code(200);
                echo json_encode(['status' => 'already_processed']);
                return;
            }
            
            // Update payment status
            $status = $paymentData['status'] ?? 'pending';
            $this->db->execute(
                'UPDATE payments SET status = ?, payment_data = ? WHERE id = ?',
                [$status, json_encode($paymentData), $payment['id']]
            );
            
            // If approved, add days to user
            if ($status === 'approved') {
                $user = User::findById($this->db, $payment['user_id']);
                
                if ($user) {
                    $user->addDays($payment['days_added']);
                    error_log("Added {$payment['days_added']} days to user {$user->id}");
                }
            }
            
            http_response_code(200);
            echo json_encode(['status' => 'processed', 'payment_status' => $status]);
            
        } catch (Exception $e) {
            error_log('Webhook processing error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    private function getMercadoPagoPayment($accessToken, $paymentId) {
        $url = "https://api.mercadopago.com/v1/payments/{$paymentId}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Failed to get payment from Mercado Pago');
        }
        
        return json_decode($response, true);
    }
}
