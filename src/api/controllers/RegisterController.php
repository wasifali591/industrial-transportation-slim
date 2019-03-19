<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\validation\Validator;

require_once __DIR__ . '/../../constants/EndPoints.php';
require_once __DIR__ . '/../../constants/StatusCode.php';

$app->post(USER_REGISTER_API_END_POINT, function (Request $request, Response $response) {
	// Fetching filemaker connection from container 'db'
	$fm = $this->get('db');

	// Receiving values from Angular and assigning it to a variable
	$firstName = $request->getParsedBody()['firstName'];
	$lastName = $request->getParsedBody()['lastName'];
	$Email = $request->getParsedBody()['Email'];
	$userType = $request->getParsedBody()['userType'];
	$password = $request->getParsedBody()['password'];


	// Checking if any of the fields are empty
	if ($firstName == '' || $lastName == '' || $Email == '' || $userType == '' || $password == '') {
		return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], USER_NOT_FOUND);
	} else {
		$validator=new Validator();
		$validateEmail = $validator->ValidateEmail($Email);
		if ($validateEmail == false) {			
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
			exit();
		}

		$validatePassword = $validator->ValidatePassword($password);
		if ($validatePassword == false) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
		}

		// generate hashcode using bcrypt technique with cost 10
		$options = [
			'cost' => 10
		];
		$hashCode = password_hash($password, PASSWORD_BCRYPT, $options);

		$fmquery = $fm->newAddCommand("UserLayout");
		$fmquery->setField("UserType_xt", $userType);
		$fmquery->setField("UserFirstName_xt", $firstName);
		$fmquery->setField("UserLastName_xt", $lastName);
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
