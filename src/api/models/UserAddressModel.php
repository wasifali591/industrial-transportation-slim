<?php
/**
 * User Adress Layout Model
 * Created date : 09/04/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Conatin one property($_layoutName) and three method(updateProfile,
 * viewProfile, createPlaceForAddress)
 */
class UserAddressModel
{
    private $_layoutName="UserAddressLayout";
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
            "__kf_UserId_xn"=> $requestValue['__kf_UserId_xn']
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
        if (is_string($results)) {
            return $results;
        }
        return "UPDATE_SUCCESSFULLY";
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
    /**
     * Insert user id into Address table for further use
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function createPlaceForAddress($requestValue, $container)
    {
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        $results=$instance->createRecord($this->_layoutName, $requestValue, $container);
        return $results;
    }
}
