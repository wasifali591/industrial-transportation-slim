<?php
/**
 * Generate Hash Code 
 * Created date : 25/03/2019
 * 
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

/**
 * Ganerate hashcode of the input taken as an argument using bcrypt alogo and
 * cost 10 and return the hash
 * 
 * @param string $password hold the user password 
 * 
 * @return string 
 */
function hashCode($password)
{
    $options = [
            'cost' => 10
        ];
    $hashCode = password_hash($password, PASSWORD_BCRYPT, $options);
    return $hashCode;
}
