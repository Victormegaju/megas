<?php
// Router class to handle request routing
class Router {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }
    
    public function route($uri, $method) {
        // API routes
        if (strpos($uri, '/api/') === 0) {
            $this->routeApi($uri, $method);
            return;
        }
        
        // Dashboard routes
        if ($uri === '/dashboard' || $uri === '/') {
            $this->routeDashboard();
            return;
        }
        
        // Admin routes
        if ($this->user->isAdmin()) {
            if (strpos($uri, '/admin/') === 0) {
                $this->routeAdmin($uri, $method);
                return;
            }
        }
        
        // Reseller routes
        if ($this->user->isRevenda()) {
            if (strpos($uri, '/revenda/') === 0) {
                $this->routeRevenda($uri, $method);
                return;
            }
        }
        
        // User routes
        if (strpos($uri, '/user/') === 0 || $uri === '/chat') {
            $this->routeUser($uri, $method);
            return;
        }
        
        // Profile route (all roles)
        if ($uri === '/profile') {
            require_once __DIR__ . '/views/profile.php';
            return;
        }
        
        // Payment routes (resellers and users)
        if (strpos($uri, '/payment/') === 0) {
            require_once __DIR__ . '/controllers/PaymentController.php';
            $controller = new PaymentController($this->db, $this->user);
            $controller->handle($uri, $method);
            return;
        }
        
        // 404
        http_response_code(404);
        echo '404 - Not Found';
    }
    
    private function routeDashboard() {
        if ($this->user->isAdmin()) {
            require_once __DIR__ . '/views/admin/dashboard.php';
        } elseif ($this->user->isRevenda()) {
            require_once __DIR__ . '/views/revenda/dashboard.php';
        } else {
            require_once __DIR__ . '/views/user/dashboard.php';
        }
    }
    
    private function routeAdmin($uri, $method) {
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($this->db, $this->user);
        $controller->handle($uri, $method);
    }
    
    private function routeRevenda($uri, $method) {
        require_once __DIR__ . '/controllers/RevendaController.php';
        $controller = new RevendaController($this->db, $this->user);
        $controller->handle($uri, $method);
    }
    
    private function routeUser($uri, $method) {
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController($this->db, $this->user);
        $controller->handle($uri, $method);
    }
    
    private function routeApi($uri, $method) {
        require_once __DIR__ . '/controllers/ApiController.php';
        $controller = new ApiController($this->db, $this->user);
        $controller->handle($uri, $method);
    }
}
