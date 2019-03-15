<?php
use Slim\Http\Response;

require_once __DIR__ . '/../constants/StatusCode.php';

function validate(string $Email, string $password, Response $response){
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
    }

    if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
        return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
    }
}
