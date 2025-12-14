<?php
// Reseller Controller
class RevendaController {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
        
        if (!$this->user->isRevenda()) {
            http_response_code(403);
            die('Access denied');
        }
    }
    
    public function handle($uri, $method) {
        if ($uri === '/revenda/dashboard') {
            require_once __DIR__ . '/../views/revenda/dashboard.php';
        } elseif ($uri === '/revenda/users') {
            require_once __DIR__ . '/../views/revenda/users.php';
        } elseif ($uri === '/revenda/profile') {
            require_once __DIR__ . '/../views/revenda/profile.php';
        } elseif (strpos($uri, '/revenda/api/') === 0) {
            $this->handleApi($uri, $method);
        } else {
            http_response_code(404);
            echo '404 - Not Found';
        }
    }
    
    private function handleApi($uri, $method) {
        header('Content-Type: application/json');
        
        if ($uri === '/revenda/api/users' && $method === 'GET') {
            $this->listMyUsers();
        } elseif ($uri === '/revenda/api/users' && $method === 'POST') {
            $this->createUser();
        } elseif (preg_match('#^/revenda/api/users/(\d+)$#', $uri, $matches) && $method === 'PUT') {
            $this->updateUser($matches[1]);
        } elseif (preg_match('#^/revenda/api/users/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            $this->deleteUser($matches[1]);
        } elseif (preg_match('#^/revenda/api/users/(\d+)/suspend$#', $uri, $matches) && $method === 'POST') {
            $this->suspendUser($matches[1]);
        } elseif (preg_match('#^/revenda/api/users/(\d+)/activate$#', $uri, $matches) && $method === 'POST') {
            $this->activateUser($matches[1]);
        } elseif (preg_match('#^/revenda/api/users/(\d+)/renew$#', $uri, $matches) && $method === 'POST') {
            $this->renewUser($matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
    }
    
    private function listMyUsers() {
        $users = $this->user->getCreatedUsers();
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
    
    private function createUser() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['username']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
        
        $user = new User($this->db);
        $user->username = $data['username'];
        $user->role = 'usuario';
        $user->password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->is_active = 1;
        $user->created_by = $this->user->id;
        $user->is_test_user = $data['is_test_user'] ?? 0;
        
        // Handle test user durations
        if ($user->is_test_user) {
            $hours = $data['test_hours'] ?? 24;
            $expiration = new DateTime();
            $expiration->modify("+{$hours} hours");
            $user->expiration_date = $expiration->format('Y-m-d H:i:s');
        } else {
            $days = $data['days'] ?? 30;
            $expiration = new DateTime();
            $expiration->modify("+{$days} days");
            $user->expiration_date = $expiration->format('Y-m-d H:i:s');
        }
        
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
        
        if (!$user || $user->created_by != $this->user->id) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found or access denied']);
            return;
        }
        
        if (isset($data['username'])) $user->username = $data['username'];
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
        
        if (!$user || $user->created_by != $this->user->id) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found or access denied']);
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
        
        if (!$user || $user->created_by != $this->user->id) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found or access denied']);
            return;
        }
        
        $user->is_active = 0;
        $user->save();
        echo json_encode(['success' => true]);
    }
    
    private function activateUser($userId) {
        $user = User::findById($this->db, $userId);
        
        if (!$user || $user->created_by != $this->user->id) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found or access denied']);
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
        
        if (!$user || $user->created_by != $this->user->id) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found or access denied']);
            return;
        }
        
        $user->addDays($days);
        echo json_encode(['success' => true, 'new_expiration' => $user->expiration_date]);
    }
}
