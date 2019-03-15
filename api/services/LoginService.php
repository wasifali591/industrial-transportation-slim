<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

require_once __DIR__ . '/../../constants/StatusCode.php';


function checkLogIn(string $Email, string $password, Response $response,  $context){
    if ($Email != '' && $password != '') {

        //validate password and email
        //validate($Email, $password, $response);

        $fm = $context->get('db');
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();

        if (FileMaker::isError($result)) {
            return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], UNAUTHORIZED_USER);
        } else {
            $records = $result->getRecords();
            $record = $records[0];
            $currentId = $record->getField('___kp_UserId_xn');

            $fmquery = $fm->newFindCommand("UserCredentialsLayout");
            $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
            $result = $fmquery->execute();
            $records = $result->getRecords();
            $record = $records[0];
            $hash = $record->getField('CurrentPassword_xt');


            if (password_verify($password, $hash)) {
                $settings = $context->get('settings'); //get settings array
                $token = JWT::encode(['id' => $currentId, 'email' => $Email], $settings['jwt']['secret'], "HS256");
                return $response->withJSON(['token' => $token], SUCCESS_RESPONSE);
            } else {
                return $response->withJSON(['error' => true, 'message' => 'Invalid Email or Password.'], INVALID_USER_PASS);
            }
        }
    }
}

