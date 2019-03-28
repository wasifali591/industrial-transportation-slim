<?php
/**
 * File Name  : UserProfileModel
* Description : insert and fetch user information from database
* Created date : 26/03/2019
* Author  : Md Wasif Ali
* Comments :
 */

namespace App\api\models;

use Pimple\Psr11\Container;

/**
 * class-name:UserProfileModel
 * description:
 */
class UserProfileModel
{
    /**
     * function-name:updateUserProfileModel
     * @param $requestValue
     * @param $container
     * description: update the user profile
     */
    public function updateUserProfileModel($requestValue, $container)
    {
        $fm = $container;

        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('___kp_UserId_xn', '==' . $requestValue['id']);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "USER_NOT_MATCHED";
        }
        $records = $result->getRecords();
        $record = $records[0];
        $recordId = $record->getRecordID(); //$recordId is the actual id of the record generated by id

        $fmquery = $fm->newEditCommand('UserLayout', $recordId);
        $fmquery->setField('Gender_xt', $requestValue['gender']);
        $fmquery->setField('DateOfBirth_xd', $requestValue['dob']);
        $fmquery->setField('Mobile_xn', $requestValue['mobile']);
        $fmquery->setField('GovernmentIdType_xt', $requestValue['idType']);
        $fmquery->setField('GovernmentIdNumber_xt', $requestValue['idNumber']);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "SERVER_ERROR";
        }
        return "UPDATE_SUCCESSFULLY";
    }

    public function viewUserProfile($id, $container)
    {
        $fm = Container;
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('___kp_UserId_xn', '==' . $id);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "USER_NOT_MATCHED";
        }
        $records = $result->getRecords();
        $record = $records[0];
        $recordId = $record->getRecordID();
    }
}
