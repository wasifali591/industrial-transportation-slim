<?php
namespace App\api\models;

require_once __DIR__ . '/../../constants/StatusCode.php';

class LoginModel{
    public function checkLogIn(string $Email, $container){
        $fm = $container;
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return false;
        }
        $records = $result->getRecords();
        $record = $records[0];
        $currentId = $record->getField('___kp_UserId_xn');
        $fmquery = $fm->newFindCommand("UserCredentialsLayout");
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
        $result = $fmquery->execute();
        $records = $result->getRecords();
        $record = $records[0];
        $hash = $record->getField('CurrentPassword_xt');

        $loginResponse = array(
            "id" => $currentId,
            "password" => $hash
        );

        return $loginResponse;
    }
}
