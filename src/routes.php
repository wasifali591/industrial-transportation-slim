<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once "constants/EndPoints.php";

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// public api, which can access anyone without any authentication
$app->group('/public/v1', function(\Slim\App $app) {
    $app->post(USER_LOGIN_API_END_POINT,'LoginController:checkLogin');
    $app->post(USER_REGISTER_API_END_POINT,'RegisterController:register');
});

// private api, to access need a token 
$app->group('/private/v1', function(\Slim\App $app) {
    $app->post('/change-password','PasswordController:changePassword');
    $app->post('/update-user-information','UserProfileController:updateUserProfile');
    
});
