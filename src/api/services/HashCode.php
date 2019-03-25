<?php
/**
 * File Name  : HashCode
* Description : generate hashcode
* Created date : 25/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */
    /**
     * function-name:hashCode
     * @param $password
     * description: generate hashcode of the password
     */
    function hashCode($password){
        $options = [
                'cost' => 10
            ];
            $hashCode = password_hash($password, PASSWORD_BCRYPT, $options);
            return $hashCode;
    }