<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use \Firebase\JWT\JWT;

require __DIR__ . '/../../validation/validator.php';
require_once __DIR__ . '/../../constants/EndPoints.php';
require __DIR__ . '/../services/LoginService.php';

$app->post(USER_LOGIN_API_END_POINT, function (Request $request, Response $response) {
    $Email = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];

    if ($Email == '' || $password == '') {
        return $response->withJSON(['error' => true, 'message' => 'Email or Password is empty.'], UNAUTHORIZED_USER);
     } else {
        $validateEmail = ValidateEmail($Email);
        if ($validateEmail == false) {
            $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
        }

        $validatePassword = ValidatePassword($password);
        if ($validatePassword == false) {
            $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
        }

        $loginResponse = checkLogIn($Email, $password, $this);
        if ($loginResponse == false) {
            return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], USER_NOT_FOUND);
        } else {
            if (password_verify($password, $loginResponse['password'])) {
                $settings = $this->get('settings'); //get settings array
                $token = JWT::encode(['id' => $loginResponse['id'], 'email' => $Email], $settings['jwt']['secret'], "HS256");
                return $response->withJSON(['token' => $token], SUCCESS_RESPONSE);
            } else {
                return $response->withJSON(['error' => true, 'message' => 'Invalid Email or Password.'], INVALID_CREDINTIAL);
            }
        }
    }
});
