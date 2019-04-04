<?php
/**
 * Registration
 *
 * For registration at first check the email is present in the db or not, if not
 * present then insert the data into db
 * Created date : 19/03/2019
 * 
 * PHP version 5
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Contain  one method(Registration)
 */
class UserModel
{
    private $_layoutName="UserLayout";
    /**
     * Check the email,which is passed through arguments($requestValue) is
     * present in the db or not, if not present then insert the data into db
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function registration(array $requestValue, $container)
    {
        /**
         * Used to store db information
         *
         * @var object
         */
        $fm = $container;
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $crud=new CRUDOperation();
        //find the email is in db(UserLayout) or not
        $fieldsName=array(
            "Email_xt"=>$requestValue['email']
        );
        $result=$crud->findRecord($this->_layoutName, $fieldsName, $fm);
        
        //if email is not present in the db then insert the input into db
        if (!$result) {
            $fieldsName=array(
                "UserType_xt"=>$requestValue['userType'],
                "UserFirstName_xt"=>$requestValue['firstName'],
                "UserLastName_xt"=>$requestValue['lastName'],
                "Email_xt"=>$requestValue['email'],
            );
            $result=$crud->createRecord($this->_layoutName, $fieldsName, $fm);
            if (is_string($result)) {
                return $result;
            }
            $lastID = $result['___kp_UserId_xn'];

            $fieldsName=array(
                "__kf_UserId_xn"=>$lastID,
                "Password_xt"=>$requestValue['password'],
                "Flag_xt"=>"active",
            );
            $result=$crud->createRecord("UserCredentialsLayout", $fieldsName, $fm);

            if (is_string($result)) {
                return $result;
            }
        } else {
            return "ALREADY_REGISTERED";
        }
        return "SUCCESSFULLY_REGISTER";
    }

    /**
     * Search for any data is available as user information or not,
     * if available then get al the related data
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function searchRecord($requestValue, $container)
    {
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $crud=new CRUDOperation();
        $result=$crud->findRecord($this->_layoutName, $requestValue, $container);
        return $result;
    }
}
