<?php
namespace App\api\services;

require_once __DIR__ . '/../../constants/StatusCode.php';

class Validator{
    function ValidateEmail(string $Email){
        if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
    }

    function ValidatePassword(string $password){
        if (preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
            return true;
        }
    }
}
