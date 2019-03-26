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
require_once __DIR__ .'/../services/HashCode.php';

/**
 * class-name:RegisterController
 * description:
 */
class RegisterController
{
	public $container; //variable to contain the db instance
	public $settings; //variable to contain the settings

	/**
     * a constructor to initialize the FileMaker instance and get the settings
     * @param $container
     */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container->get('db');
		$this->settings = $container->get('settings');
	}

	/**
     * function-name:Register
     * @param $request
     * @param $request
     * description: read the input and chek for validation , for valid data insert into db
     */
	public function register(Request $request, Response $response)
	{
		// Receiving values from Angular and assigning it to a variable
		$firstName = $request->getParsedBody()['firstName'];
		$lastName = $request->getParsedBody()['lastName'];
		$Email = $request->getParsedBody()['email'];
		$userType = $request->getParsedBody()['userType'];
		$password = $request->getParsedBody()['password'];
		$confirmPassword = $request->getParsedBody()['confirmPassword'];

		// Checking if any of the fields are empty
		if ($firstName == '' || $lastName == '' || $Email == '' || $userType == '' || $password == '' || $confirmPassword == '') {
			return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
		}
		//create instance of Validator
		$validator = new Validator();
		//function(ValidateEmail) call to check email validation
		$validateEmail = $validator->validateEmail($Email);

		//if invalid email then return an error with an error message
		if (!$validateEmail) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
		}
		//function(ValidatePassword) call to check password validation
		$validatePassword = $validator->validatePassword($password);

		//if password is not matched with the required pattern the return an error with an error message
		if (!$validatePassword) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
		}
		//if password and confirm password are not match return an error message
		if ($password !== $confirmPassword) {
			return $response->withJSON(['error' => true, 'message' => 'Password and Conform Password are not match.'], INVALID_CREDINTIAL);
		}

		// generate hashcode using bcrypt technique with cost 10
		// $options = [
		// 	'cost' => 10
		// ];
		// $hashCode = password_hash($password, PASSWORD_BCRYPT, $options);
		$hashCode=hashCode($password);
		//makeing an array of valid inputs
		$requestValue = array(
			"userType" => $userType,
			"firstName" => $firstName,
			"lastName" => $lastName,
			"email" => $Email,
			"password" => $hashCode
		);
		//instance of RegisterModel
		$registration = new RegisterModel();
		//function(Registration) call
		$value = $registration->Registration($requestValue, $this->container);
		//get the settings for responseMessage
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
	}
}
