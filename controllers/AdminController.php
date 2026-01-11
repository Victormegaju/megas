<?php
// Admin Controller
class AdminController {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
        
        if (!$this->user->isAdmin()) {
            http_response_code(403);
            die('Access denied');
        }
    }
    
    public function handle($uri, $method) {
        if ($uri === '/admin/dashboard') {
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } elseif ($uri === '/admin/users') {
            require_once __DIR__ . '/../views/admin/users.php';
        } elseif ($uri === '/admin/resellers') {
            require_once __DIR__ . '/../views/admin/resellers.php';
        } elseif ($uri === '/admin/settings') {
            if ($method === 'POST') {
                $this->updateSettings();
            } else {
                require_once __DIR__ . '/../views/admin/settings.php';
            }
        } elseif (strpos($uri, '/admin/api/') === 0) {
            $this->handleApi($uri, $method);
        } else {
            http_response_code(404);
            echo '404 - Not Found';
        }
    }
    
    private function handleApi($uri, $method) {
        header('Content-Type: application/json');
        
        if ($uri === '/admin/api/users' && $method === 'GET') {
            $this->listUsers();
        } elseif ($uri === '/admin/api/users' && $method === 'POST') {
            $this->createUser();
        } elseif (preg_match('#^/admin/api/users/(\d+)$#', $uri, $matches) && $method === 'PUT') {
            $this->updateUser($matches[1]);
        } elseif (preg_match('#^/admin/api/users/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            $this->deleteUser($matches[1]);
        } elseif (preg_match('#^/admin/api/users/(\d+)/suspend$#', $uri, $matches) && $method === 'POST') {
            $this->suspendUser($matches[1]);
        } elseif (preg_match('#^/admin/api/users/(\d+)/activate$#', $uri, $matches) && $method === 'POST') {
            $this->activateUser($matches[1]);
        } elseif (preg_match('#^/admin/api/users/(\d+)/renew$#', $uri, $matches) && $method === 'POST') {
            $this->renewUser($matches[1]);
        } elseif ($uri === '/admin/api/resellers' && $method === 'GET') {
            $this->listResellers();
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
    }
    
    private function listUsers() {
        $users = User::getAll($this->db, 'usuario');
        echo json_encode(array_map(function($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'expiration_date' => $user->expiration_date,
                'is_active' => $user->is_active,
                'is_test_user' => $user->is_test_user,
                'remaining_days' => $user->getRemainingDays()
            ];
        }, $users));
    }
    
    private function listResellers() {
        $resellers = User::getAll($this->db, 'revenda');
        echo json_encode(array_map(function($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'expiration_date' => $user->expiration_date,
                'is_active' => $user->is_active,
                'remaining_days' => $user->getRemainingDays()
            ];
        }, $resellers));
    }
    
    private function createUser() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['username']) || empty($data['password']) || empty($data['role'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
        
        $user = new User($this->db);
        $user->username = $data['username'];
        $user->role = $data['role'];
        $user->password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->is_active = $data['is_active'] ?? 1;
        
        $days = $data['days'] ?? 30;
        $expiration = new DateTime();
        $expiration->modify("+{$days} days");
        $user->expiration_date = $expiration->format('Y-m-d H:i:s');
        
        try {
            $user->save();
            echo json_encode(['success' => true, 'id' => $user->id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }
    
    private function updateUser($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = User::findById($this->db, $userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        if (isset($data['username'])) $user->username = $data['username'];
        if (isset($data['role'])) $user->role = $data['role'];
        if (isset($data['is_active'])) $user->is_active = $data['is_active'];
        if (isset($data['expiration_date'])) $user->expiration_date = $data['expiration_date'];
        
        if (!empty($data['password'])) {
            $user->updatePassword($data['password']);
        }
        
        try {
            $user->save();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user']);
        }
    }
    
    private function deleteUser($userId) {
        $user = User::findById($this->db, $userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        try {
            $user->delete();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user']);
        }
    }
    
    private function suspendUser($userId) {
        $user = User::findById($this->db, $userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        $user->is_active = 0;
        $user->save();
        echo json_encode(['success' => true]);
    }
    
    private function activateUser($userId) {
        $user = User::findById($this->db, $userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        $user->is_active = 1;
        $user->save();
        echo json_encode(['success' => true]);
    }
    
    private function renewUser($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $days = $data['days'] ?? 30;
        
        $user = User::findById($this->db, $userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        $user->addDays($days);
        echo json_encode(['success' => true, 'new_expiration' => $user->expiration_date]);
    }
    
    private function updateSettings() {
        header('Content-Type: application/json');
        
        $settings = new Settings($this->db);
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['logo']['type'];
            
            if (in_array($fileType, Constants::ALLOWED_IMAGE_TYPES)) {
                if ($_FILES['logo']['size'] > Constants::MAX_LOGO_SIZE) {
                    echo json_encode(['error' => 'File too large. Maximum size is 2MB']);
                    return;
                }
                
                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $extension;
                $uploadPath = __DIR__ . '/../uploads/logo/' . $filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    // Delete old logo
                    $oldLogo = $settings->getSiteLogo();
                    if ($oldLogo) {
                        $oldPath = __DIR__ . '/../uploads/logo/' . $oldLogo;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $settings->set('site_logo', $filename);
                }
            }
        }
        
        // Update other settings
        if (isset($_POST['gemini_api_key'])) {
            $settings->set('gemini_api_key', $_POST['gemini_api_key']);
        }
        if (isset($_POST['gemini_model'])) {
            $settings->set('gemini_model', $_POST['gemini_model']);
        }
        if (isset($_POST['mp_access_token'])) {
            $settings->set('mp_access_token', $_POST['mp_access_token']);
        }
        if (isset($_POST['mp_public_key'])) {
            $settings->set('mp_public_key', $_POST['mp_public_key']);
        }
        if (isset($_POST['mp_webhook_signature_key'])) {
            $settings->set('mp_webhook_signature_key', $_POST['mp_webhook_signature_key']);
        }
        if (isset($_POST['mp_payments_enabled'])) {
            $settings->set('mp_payments_enabled', $_POST['mp_payments_enabled'] ? '1' : '0');
        }
        
        echo json_encode(['success' => true]);
    }
}
