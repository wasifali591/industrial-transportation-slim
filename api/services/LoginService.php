<?php

require_once __DIR__ . '/../../constants/StatusCode.php';
require __DIR__.'/../../core/models/LoginResponse.php';


function checkLogIn(string $Email, string $password, $context)
{
    
        $fm = $context->get('db');
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();

        if (FileMaker::isError($result)) {
           return false;
          
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

            $loginResponse=array(
                "id"=>$currentId,
                "password"=>$hash
            );

            return $loginResponse;
        }
    }
