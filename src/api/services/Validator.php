<?php
/**
 * Check data is valid or not
 * Created date : 19/03/2019
 *
 * PHP version 5
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\services;

require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * Contain two method(validateEmail, validatePassword)
 */
class Validator
{
    /**
     * Check the input data is a valid email or not
     *
     * @param string $email mail assign by the user
     *
     * @return bool
     */
    public function validateEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    /**
     * Check the input data is a valid password  or not ,
     * match with a pattarn declared using regular expression
     *
     * @param string $password password assign by the user
     *
     * @return bool
     */
    public function validatePassword(string $password)
    {
        if (preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
            return true;
        }
        return false;
    }
    /**
     * Check the mobile no is valid or not ,
     * match with a pattarn declared using regular expression
     *
     * @param string $mobile mobile assign by the user
     *
     * @return bool
     */
    public function validateMobile($mobile)
    {
        if (preg_match('/^[0-9]{10}+$/', $mobile)) {
            return true;
        }
        return false;
    }
}
