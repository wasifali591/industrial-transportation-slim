<?php
/**
 * User Adress Layout Model
 * Created date : 09/04/2019
 *
 * PHP version 5
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Conatin one property($_layoutName) and one method(updateProfile)
 */
class UserAddressLayoutModel
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
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        $results=$instance->createRecord($this->_layoutName, $requestValue, $container);
        if (is_string($results)) {
            return $results;
        }
        return "UPDATE_SUCCESSFULLY";
    }
}
