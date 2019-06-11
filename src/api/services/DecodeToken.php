<?php
/**
 * Decode token
 *
 * Decode token and get the user id from the token
 * Created date : 29/03/2019
 *
 * PHP version 7
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
use \Firebase\JWT\JWT;

/** 
 * Decode token
 * 
 * Read the header from request and decode the header, and put the data into an array.
 * 
 * @return int $id return user id
 */
function decodeToken()
{
    $headers = apache_request_headers();
    $string = $headers['Authorization'];
    $str_arr = preg_split("/\ /", $string);
    $decoded = JWT::decode($str_arr[1], "truckage", array('HS256'));
    $decoded_array = (array) $decoded;
    $id=$decoded_array['id'];
    return $id;
}
