<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

$app->post('/login', function (Request $request, Response $response) {
    $fm = $this->get('db');

    $Email = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];

    if ($Email != '' && $password != '') {
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();

        if (FileMaker::isError($result)) {
            return $response->withJSON(['error' => true, 'message' => 'Email is not correct.'], 404);
        } else {
            $records = $result->getRecords();
            $record = $records[0];
            $currentId = $record->getField('___kp_UserId_xn');

            $fmquery = $fm->newFindCommand("UserCredentialsLayout");
            $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
            $result = $fmquery->execute();
            $records = $result->getRecords();
            $record = $records[0];
            $currentPassword = $record->getField('CurrentPassword_xt');

            if ($password == $currentPassword) {
                $settings = $this->get('settings'); //get settings array
                $token = JWT::encode(['id' => $currentId, 'email' => $Email], $settings['jwt']['secret'], "HS256");
                return $response->withJSON(['token' => $token], 201);
            } else {
                return $response->withJSON(['error' => true, 'message' => 'Password is not correct.'], 404);
            }
        }
    }
});
