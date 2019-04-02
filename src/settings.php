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
            'host' => '172.16.9.42',//172.16.8.217
            'database' => 'industrial-transport',
            'username' => 'admin',
            'password' => 'mindfire',
        ],

        //jwt settings
        'jwt'=>[
            'secret' => 'truckage'
        ],

        //responsMessage for each type of response and their corrosponding message and status code
        'responsMessage'=>[
            'SERVER_ERROR'=>['error'=>true,
                             'message'=>'Internal server error.',
                             'statusCode'=>500],
            'USER_NOT_MATCHED'=>['error'=>true,
                                 'message'=>'User not found. Please register first.',
                                 'statusCode'=>400],
            'OLD_PASSWORD'=>['error'=>true,
                             'message'=>'Your new password is previously used.Try with a different password',
                             'statusCode'=>400],
            'PASSWORD_NOT_MATCHED'=>['error'=>true,
                             'message'=>'Password is not correct.',
                             'statusCode'=>403],
            'ALREADY_REGISTERED'=>['error'=>true,
                             'message'=>'Already Registered. Try with another email.',
                             'statusCode'=>409],
            'DOCUMENT_NOT_FOUND'=>['error'=>true,
                             'message'=>'Document not found.',
                             'statusCode'=>404],
            'TRUCK_ALREADY_REGISTERED'=>['error'=>true,
                             'message'=>'Truck is already registered.',
                             'statusCode'=>409],
            'NOT_FOUND'=>['error'=>true,
                             'message'=>'Not found.',
                             'statusCode'=>404],

            'PASSWORD_CHANGED'=>['error'=>false,
                             'message'=>'Yor password is successfully changed.',
                             'statusCode'=>201],
            'SUCCESSFULLY_REGISTER'=>['error'=>false,
                             'message'=>'Successfully registered.',
                             'statusCode'=>201],
            'UPDATE_SUCCESSFULLY'=>['error'=>false,
                             'message'=>'Successfully uptadeted.',
                             'statusCode'=>201],
            
        ],

    ],
];
