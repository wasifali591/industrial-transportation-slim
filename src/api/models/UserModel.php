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
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        //find the email is in db(UserLayout) or not
        $fieldsName=array(
            "Email_xt"=>$requestValue['Email_xt']
        );
        $results=$instance->findRecord($this->_layoutName, $fieldsName, $container);
        //if email is not present in the db then insert the input into db
        if (is_string($results)) {
            $results=$instance->createRecord($this->_layoutName, $requestValue, $container);
            return $results;
        }
        return "ALREADY_REGISTERED";
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

    /**
     * Update user profile
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function updateProfile($requestValue, $container)
    {
        $fieldsName=array(
            "___kp_UserId_xn"=> $requestValue['___kp_UserId_xn']
        );
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        $results=$instance->findRecord($this->_layoutName, $fieldsName, $container);
        if (is_string($results)) {
            return $results;
        }
        $result=$results[0];
        $recordId=$result['recordId'];
        $results=$instance->editRecord($this->_layoutName, $recordId, $requestValue, $container);
        
        return $results;
    }
    /**
     * View user profile
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function viewProfile($requestValue, $container)
    {
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        $results=$instance->findRecord($this->_layoutName, $requestValue, $container);
        return $results;
    }
}
