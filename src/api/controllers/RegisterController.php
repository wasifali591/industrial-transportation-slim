<?php
/**
 * File Name  : RegisterController
* Description : php code to take input from angular form and insert into db 
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */

namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use App\api\services\Validator;
use App\api\models\RegisterModel;

require_once __DIR__ . '/../../constants/EndPoints.php';
require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * class-name:RegisterController
 * description:
 */
class RegisterController{
	public $container; //variable to contain the db instance

	/**
     * a constructor to initialize the FileMaker instance and get the settings
     * @param $container
     */
	public function __construct(ContainerInterface $container){
		$this->container = $container->get('db');
	}

	/**
     * function-name:Register
     * @param $request
     * @param $request
     * description: read the input and chek for validation , for valid data insert into db
     */
	public function Register(Request $request, Response $response){
		// Receiving values from Angular and assigning it to a variable
		$firstName = $request->getParsedBody()['firstName'];
		$lastName = $request->getParsedBody()['lastName'];
		$Email = $request->getParsedBody()['email'];
		$userType = $request->getParsedBody()['userType'];
		$password = $request->getParsedBody()['password'];

		// Checking if any of the fields are empty
		if ($firstName == '' || $lastName == '' || $Email == '' || $userType == '' || $password == '') {
			return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], USER_NOT_FOUND);
		}
		//create instance of Validator
		$validator = new Validator();
		//function(ValidateEmail) call to check email validation
		$validateEmail = $validator->ValidateEmail($Email);

		//if invalid email then return an error with an error message
		if (!$validateEmail) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
		}
		//function(ValidateEmail) call to check password validation
		$validatePassword = $validator->ValidatePassword($password);

		//if password is not matched with the required pattern the return an error with an error message
		if (!$validatePassword) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
		}

		// generate hashcode using bcrypt technique with cost 10
		$options = [
			'cost' => 10
		];
		$hashCode = password_hash($password, PASSWORD_BCRYPT, $options);
		//makeing an array of valid inputs
		$requestValue = array(
			"userType" => $userType,
			"firstName" => $firstName,
			"lastName" => $lastName,
			"email"=>$Email,
			"password" => $hashCode
		);
		//instance of RegisterModel
		$registration = new RegisterModel();
		//function(Registration) call
		$result = $registration->Registration($requestValue, $this->container);

		if ($result){
			return $response->withJSON(['error' => true, 'message' => 'Registration failed.'], INTERNAL_SERVER_ERROR);
		}
		return $response->withJSON(['message' => 'Successfully registered.'], SUCCESS_RESPONSE);
	}
}
