<?php
// Configuration Template
// Copy this file to config.php and update with your database credentials

return [
    'db' => [
        'host' => 'localhost',
        'database' => 'megas_db',
        'username' => 'your_db_user',
        'password' => 'your_db_password',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'Megas Chat',
        'timezone' => 'America/Sao_Paulo',
        'session_lifetime' => 7200, // 2 hours in seconds
    ],
    'upload' => [
        'logo_path' => __DIR__ . '/uploads/logo/',
        'max_size' => 2097152 // 2MB
    ]
];
