<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// include("../api/register.php");
require __DIR__.'/../api/controllers/RegisterController.php';
// include("../api/controllers/login-controller.php");
require __DIR__.'/../api/controllers/LoginController.php';
// include("../api/update-profile.php");
require __DIR__.'/../api/update-profile.php';
