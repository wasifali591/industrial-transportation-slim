<?php
/**
 * File Name  : LoginModel
* Description : php code to check the input(username and password), which is taken from angular form and check with databse and  return a response 
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */
namespace App\api\models;

//require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * class-name:LoginModel
 * description:
 */
class LoginModel{
    /**
     * function-name:checkLogin
     * @param $Email
     * @param $container
     * description: find the $Email  is present in the db or not, if present then find the corosponding password and return 
     */
    public function checkLogIn(string $Email, $container){
        $fm = $container;
        //find the $Email in db(USerLayout)
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $Email);
        $result = $fmquery->execute();

        //if $Email not found return false
        if ($fm::isError($result)) {
            return false;
        }
        //if $Email found then find the corosponding password
        $records = $result->getRecords();
        $record = $records[0];
        $currentId = $record->getField('___kp_UserId_xn');

        $fmquery = $fm->newFindCommand("UserCredentialsLayout");
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
        $result = $fmquery->execute();

        // //if $currentId not found return false
        // if ($fm::isError($result1)) {
        //     exit();
        // }
        $records = $result->getRecords();
        $record = $records[0];
        $hash = $record->getField('CurrentPassword_xt');

        //an array($loginResponse) to sore the user id and the password(hash)
        $loginResponse = array(
            "id" => $currentId,
            "password" => $hash
        );

        return $loginResponse;
    }
}
