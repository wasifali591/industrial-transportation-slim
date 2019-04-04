<?php
/**
 * Login
 * 
 * Check the input(username and password), which is taken from angular
 * form and check with databse and  return a response
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
 * Contain one method(login)
 */
class UserCredentialsModel
{
    private $_layoutName="UserCredentialsLayout";
    /**
     * Find the $Email  is present in the db or not, if present then find the
     * corosponding password and return
     * 
     * @param array  $requestValue values, which have to check is present
     *                             in the db or not
     * @param object $container    hold the db instance
     * 
     * @return multiple types of return according to the situation
     */
    public function login($requestValue, $container)
    {
        $instance=new CRUDOperation();
        $result=$instance->findRecord($this->_layoutName, $requestValue, $container);
        return $result;
    }
}
