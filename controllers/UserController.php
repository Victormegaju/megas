<?php
// User Controller
class UserController {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }
    
    public function handle($uri, $method) {
        if ($uri === '/chat' || $uri === '/user/chat') {
            require_once __DIR__ . '/../views/user/chat.php';
        } elseif ($uri === '/user/profile' || $uri === '/profile') {
            require_once __DIR__ . '/../views/user/profile.php';
        } else {
            http_response_code(404);
            echo '404 - Not Found';
        }
    }
}
