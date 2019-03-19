<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        
        //db settings
        'db' => [
            'host' => '172.16.9.42',
            'database' => 'industrial-transport',
            'username' => 'admin',
            'password' => 'mindfire',
        ],

        //jwt settings
        'jwt'=>[
            'secret' => 'truckage'
        ],

    ],
];
