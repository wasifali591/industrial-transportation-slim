<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/register', function (Request $request, Response $response) {
	// Fetching filemaker connection from container 'db'
	$fm = $this->get('db');


	// Receiving values from Angular and assigning it to a variable
	$fullName = $request->getParsedBody()['fullName'];
	$Email = $request->getParsedBody()['Email'];
	$password = $request->getParsedBody()['password'];
	//$mobile = $request->getParsedBody()['confirmPassword'];
		

	// Checking if any of the fields are empty
	if ($fullName != '' && $Email != '' && $password != '') {
			$fmquery = $fm->newAddCommand("User");
			$fmquery->setField("UserName_xt", $fullName);
			$fmquery->setField("Email_xt", $Email);
			$result = $fmquery->execute();

			$recs=$result->getRecords();
			$count=count($recs);
			$lastID=$recs[$count-1]->getRecordID();
			
			$fmquery=$fm->newAddCommand("UserCredentials");
			$fmquery->setField("__kf_UserId_xn",$lastID);
			$fmquery->setField("CurrentPassword_xt",$password);
			$result=$fmquery->execute();
	}	

	if (FileMaker::isError($result)) {
			$ErrMsg = 'Error code: ' . $result->getCode() . ' Message: ' . $result->getMessage();
			echo 'Connection Failed: ' . $ErrMsg;
			return $response->withJSON($result->getMessage(), $result->getCode());
		} else {
			return $response->withJSON('Success', 201);
		}
});
