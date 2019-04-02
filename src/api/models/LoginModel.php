<?php
/**
 * Login
 * 
 * Check the input(username and password), which is taken from angular
 * form and check with databse and  return a response
 * Created date : 19/03/2019
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

//require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * class-name:LoginModel
 * description:
 */
class LoginModel
{
    /**
     * Find the $Email  is present in the db or not, if present then find the
     * corosponding password and return
     * 
     * @param string $email 
     * @param object $container
     * description: 
     */
    public function checkLoginModel(string $email, $container)
    {
        $fm = $container;
        $flag="active";
        //find the $Email in db(USerLayout)
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('Email_xt', '==' . $email);
        $result = $fmquery->execute();

        //if $email not found return false
        if ($fm::isError($result)) {
            return "USER_NOT_MATCHED";
        }
        //if $email found then find the corosponding password
        $records = $result->getRecords();
        $record = $records[0];
        $currentId = $record->getField('___kp_UserId_xn');
        $firstNam=$record->getField('UserFirstName_xt');
        $lastName=$record->getField('UserLastName_xt');
        //find active password corosponding to the CurrentId
        $fmquery = $fm->newFindCommand("UserCredentialsLayout");
        $fmquery->setLogicalOperator('FILEMAKER_FIND_AND');
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $currentId);
        $fmquery->addFindCriterion('Flag_xt', '==' . $flag);
        $result = $fmquery->execute();
        if ($fm::isError($result)) {
            return "PASSWORD_NOT_MATCHED";
        }
        
        $records = $result->getRecords();
        $record = $records[0];
        $hash = $record->getField('Password_xt');

        //an array($loginResponse) to sore the user id and the password(hash)
        $loginResponse = array(
            "id" => $currentId,
            "firstName"=>$firstNam,
            "lastName"=>$lastName,
            "password" => $hash
        );

        return $loginResponse;
    }
}
