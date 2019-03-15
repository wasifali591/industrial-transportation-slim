<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../../constants/EndPoints.php';
require __DIR__ . '/../../constants/StatusCode.php';

$app->post(USER_REGISTER_API_END_POINT, function (Request $request, Response $response) {
	// Fetching filemaker connection from container 'db'
	$fm = $this->get('db');


	// Receiving values from Angular and assigning it to a variable
	$fullName = $request->getParsedBody()['fullName'];
	$Email = $request->getParsedBody()['Email'];
	$password = $request->getParsedBody()['password'];


	// Checking if any of the fields are empty
	if ($fullName != '' && $Email != '' && $password != '') {

		//validate password and email
        //validate($Email, $password, $response);

		// generate hashcode using bcrypt technique with cost 10
		$options = [
			'cost' => 10
		];		
		$hashCode = password_hash($password, PASSWORD_BCRYPT, $options);

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


		if (FileMaker::isError($result)) {
			return $response->withJSON(['error' => true, 'message' => 'Registration failed.'], INTERNAL_SERVER_ERROR);
		} else {
			return $response->withJSON(['message' => 'Successfully registered.'], SUCCESS_RESPONSE);
		}
	}
});
