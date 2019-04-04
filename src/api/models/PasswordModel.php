<?php
/**
 * File Name  : PasswordModel
* Description : php code to change password
* Created date : 20/03/2019
* Author  : Md Wasif Ali
* Comments :
 */

namespace App\api\models;

require_once __DIR__ .'/../services/HashCode.php';

/**
 * class-name:PasswordModel
 * description:
 */
class PasswordModel
{
    /**
     * function-name:changePassswordModel
     * @param array  $requestValue hold all the input value
     * @param Container $container contain database information
     * description:
     */
    public function changePassswordModel($requestValue, $container)
    {
        $fm = $container;
        $flag="active";
        $match=false;
        //check active password with the input
        $fmquery = $fm->newFindCommand("UserCredentialsLayout");
        $fmquery->setLogicalOperator('FILEMAKER_FIND_AND');
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $requestValue['id']);
        $fmquery->addFindCriterion('Flag_xt', '==' . $flag);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "USER_NOT_MATCHED";
        }
        $records = $result->getRecords();
        $record = $records[0];
        $activePassword = $record->getField('Password_xt');
        $recordId=$record->getRecordID();//$recordId is the actual id of the record generated by id

        if (password_verify($requestValue['oldPassword'], $activePassword)) {
            //check the new password is previously used or not
            $fmquery = $fm->newFindCommand("UserCredentialsLayout");
            $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $requestValue['id']);
            $result = $fmquery->execute();
            $records = $result->getRecords();

            foreach ($records as $record) {
                $oldPassword=$record->getField('Password_xt');

                if (password_verify($requestValue['password'], $oldPassword)) {
                    $match=true;
                    break;
                }
            }

            //check password is previously used or not
            if ($match) {
                return "OLD_PASSWORD";
            }
            // edit the previous password flag to inactive
            $record = $fm->newEditCommand('UserCredentialsLayout', $recordId);
            $record->setField('Flag_xt', "");
            $result=$record->execute();

            if ($fm::isError($result)) {
                return "SERVER_ERROR";
            }
            // generate hashcode using bcrypt technique with cost 10
            $hashCode=hashCode($requestValue['password']);
            //enter the new password and make it active
            $fmquery = $fm->newAddCommand("UserCredentialsLayout");
            $fmquery->setField("__kf_UserId_xn", $requestValue['id']);
            $fmquery->setField("Password_xt", $hashCode);
            $fmquery->setField("Flag_xt", "active");
            $result = $fmquery->execute();

            if ($fm::isError($result)) {
                return "SERVER_ERROR";
            }
            return "PASSWORD_CHANGED";
        }
        return "PASSWORD_NOT_MATCHED";
    }
}
