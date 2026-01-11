<?php
// Authentication Controller
class AuthController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function login() {
        header('Content-Type: application/json');
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }
        
        $user = User::authenticate($this->db, $username, $password);
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }
        
        if (!$user->isActive()) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Account expired',
                'expired' => true,
                'expiration_date' => $user->expiration_date
            ]);
            return;
        }
        
        // Set session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;
        $_SESSION['last_activity'] = time();
        
        // Determine redirect based on role
        $redirect = '/dashboard';
        if ($user->isAdmin()) {
            $redirect = '/admin/dashboard';
        } elseif ($user->isRevenda()) {
            $redirect = '/revenda/dashboard';
        } else {
            $redirect = '/chat';
        }
        
        echo json_encode([
            'success' => true,
            'redirect' => $redirect,
            'role' => $user->role
        ]);
    }
    
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
