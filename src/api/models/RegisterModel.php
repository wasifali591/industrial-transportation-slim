<?php
/**
 * File Name  : RegisterModel
* Description : php code to insert input date into db
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */

namespace App\api\models;

/**
 * class-name:RegisterModel
 * description:
 */
class RegisterModel{
    /**
     * function-name:Registration
     * @param $requestValue
     * @param $container
     * description: insert input data into their corosponding tables 
     */
    public function Registration(array $requestValue, $container){
        $fm=$container;
        
        // $fmquery = $fm->newFindCommand("UserLayout");
        // $fmquery->addFindCriterion('Email_xt', '==' . $requestValue['email']);
        // $result = $fmquery->execute();
        // echo "wasif";
        // $records = $result->getRecords();
        // $record = $records[0];
        // $currentId = $record->getField('Email_xt');
        // echo $currentId;
        // exit();
        
        $fmquery = $fm->newAddCommand("UserLayout");
		$fmquery->setField("UserType_xt", $requestValue['userType']);
		$fmquery->setField("UserFirstName_xt", $requestValue['firstName']);
		$fmquery->setField("UserLastName_xt", $requestValue['lastName']);
		$fmquery->setField("Email_xt", $requestValue['email']);
		$result = $fmquery->execute();

		$recs = $result->getRecords();
        $record = $recs[0];
        $lastID = $record->getField('___kp_UserId_xn');

		$fmquery = $fm->newAddCommand("UserCredentialsLayout");
		$fmquery->setField("__kf_UserId_xn", $lastID);
		$fmquery->setField("CurrentPassword_xt", $requestValue['password']);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return false;
        }
    }
}