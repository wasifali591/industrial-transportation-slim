<?php
use Slim\Http\Response;

require_once __DIR__ . '/../constants/StatusCode.php';

function ValidateEmail(string $Email){
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        return false;
        //$response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
    }
}
function ValidatePassword(string $password){
    if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
        return false;
        //$response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
    }
}
