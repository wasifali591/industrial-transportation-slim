<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/register', function (Request $request, Response $response) {
	// Fetching filemaker connection from container 'db'
	$fm = $this->get('db');


	// Receiving values from Angular and assigning it to a variable
	$fullName = $request->getParsedBody()['fullName'];
	$Email = $request->getParsedBody()['Email'];
	$password = $request->getParsedBody()['password'];


	// Checking if any of the fields are empty
	if ($fullName != '' && $Email != '' && $password != '') {

		if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
		    return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], 401);
		}

		if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
		    return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], 401);
		}

		$options = [
			'cost' => 10
		];
		$hashCode= password_hash('$password', PASSWORD_BCRYPT, $options);

		$fmquery = $fm->newAddCommand("UserLayout");
		$fmquery->setField("UserName_xt", $fullName);
		$fmquery->setField("Email_xt", $Email);
		$result = $fmquery->execute();

		$recs = $result->getRecords();
		$count = count($recs);
		$lastID = $recs[$count - 1]->getRecordID();

		$fmquery = $fm->newAddCommand("UserCredentials");
		$fmquery->setField("__kf_UserId_xn", $lastID);
		$fmquery->setField("CurrentPassword_xt", $hashCode);
		$result = $fmquery->execute();
	}

	if (FileMaker::isError($result)) {
		return $response->withJSON(['error' => true, 'message' => 'Registration failed.'], 403);
	} else {
		return $response->withJSON(['message' => 'Successfully registered.'], 200);
	}
});
