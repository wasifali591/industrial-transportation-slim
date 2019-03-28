<?php
/**
 * File Name  : Validator
* Description : check validation of required field
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments :
 */

 namespace App\api\services;

require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * class-name:Validator
 * description: validate the data according to need
 */
class Validator
{
    /**
     * function-name:validateEmail
     * description:
     */
    public function validateEmail(string $Email)
    {
        if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    /**
     * function-name:validatePassword
     * description:
     */
    public function validatePassword(string $password)
    {
        if (preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
            return true;
        }
        return false;
    }
}
