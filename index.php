<?php
// Front Controller - index.php
// All requests are routed through this file

session_start();

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

// Load configuration
if (!file_exists(__DIR__ . '/config.php')) {
    if (strpos($_SERVER['REQUEST_URI'], '/install') === false) {
        header('Location: /install');
        exit;
    }
}

// Auto-loader for classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove trailing slash except for root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}

// Route the request
try {
    // Installer route (always accessible)
    if (strpos($requestUri, '/install') === 0) {
        require_once __DIR__ . '/install/index.php';
        exit;
    }

    // Load config for all other routes
    $config = require __DIR__ . '/config.php';
    
    // Initialize database
    $db = Database::getInstance($config['db']);
    
    // Public routes (no authentication required)
    if ($requestUri === '/login' || $requestUri === '/') {
        if ($requestMethod === 'POST' && $requestUri === '/login') {
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController($db);
            $controller->login();
        } else {
            require_once __DIR__ . '/views/login.php';
        }
        exit;
    }
    
    // Logout route
    if ($requestUri === '/logout') {
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        exit;
    }
    
    // Webhook route (special handling, no session check)
    if (strpos($requestUri, '/appeal/webhooks/mercadopago') === 0) {
        require_once __DIR__ . '/controllers/WebhookController.php';
        $controller = new WebhookController($db);
        $controller->handleMercadoPago();
        exit;
    }
    
    // Check authentication for all other routes
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    // Check if user is expired
    $user = User::findById($db, $_SESSION['user_id']);
    if (!$user || !$user->isActive()) {
        $_SESSION['expired'] = true;
        header('Location: /login');
        exit;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    // Route to appropriate controller based on role and path
    $router = new Router($db, $user);
    $router->route($requestUri, $requestMethod);
    
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
