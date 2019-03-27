<?php
use \Firebase\JWT\JWT;

function decodeToken(){
    $headers = apache_request_headers();
    $string = $headers['Authorization'];  
    $str_arr = preg_split ("/\ /", $string);  
    $decoded = JWT::decode($str_arr[1], "truckage", array('HS256'));
    $decoded_array = (array) $decoded;
    $id=$decoded_array['id'];
    return $id;
}