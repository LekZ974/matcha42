<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'template_cache' => __DIR__ . '/../cache/',
        ],
        // Settings database SQL
        'db' => [
            'user' => 'root',
            'pass' => 'root',
            'host' => 'localhost',
            'dbname' => 'matcha42',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
