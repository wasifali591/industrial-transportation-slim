<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/login', function (Request $request, Response $response) {
    $fm = $this->get('db');

    $Email = $request->getParsedBody()['Email'];
    $password = $request->getParsedBody()['password'];

    if ($Email != '' && $password != '') {
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();
     
        if (FileMaker::isError($result)) {
            $ErrMsg = 'Error code: ' . $result->getCode() . ' Message: ' . $result->getMessage();
             return $response->withJSON($ErrMsg, 404);
        } else {
            $records = $result->getRecords();
        $record = $records[0];
        $currentId = $record->getField('___kp_UserId_xn');

        $fmquery = $fm->newFindCommand("UserCredentialsLayout");
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
        $result = $fmquery->execute();
        $records = $result->getRecords();
        $record = $records[0];
        $currentPassword=$record->getField('CurrentPassword_xt');

        if($password == $currentPassword){
            return $response->withJSON('Success', 201);
        }else{
            return $response->withJSON('Failed',404);
        }
        }
        
    }
    
});
