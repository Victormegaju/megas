<?php
// Mercado Pago Webhook Endpoint
// This file handles webhook notifications from Mercado Pago

// Start session and load core files
session_start();
date_default_timezone_set('America/Sao_Paulo');

// Load configuration
$config = require __DIR__ . '/../../config.php';

// Auto-loader for classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../../classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize database
$db = Database::getInstance($config['db']);

// Load WebhookController
require_once __DIR__ . '/../../controllers/WebhookController.php';

// Handle the webhook
$controller = new WebhookController($db);
$controller->handleMercadoPago();
