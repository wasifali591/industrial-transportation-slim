<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__.'/api/controllers/RegisterController.php';
//require __DIR__.'/../api/controllers/LoginController.php';
//require __DIR__.'/../api/update-profile.php';
require_once "constants/EndPoints.php";

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


$app->post(USER_LOGIN_API_END_POINT,'LoginController:home');
