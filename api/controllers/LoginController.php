<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;

require __DIR__ . '/../../validation/validator.php';
require_once __DIR__ . '/../../constants/EndPoints.php';
require __DIR__ . '/../services/LoginService.php';

$app->post(USER_LOGIN_API_END_POINT, function (Request $request, Response $response) {
    $Email = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];

    //validate( $Email,  $password, $response);

     return checkLogIn($Email, $password, $response, $this);
});
