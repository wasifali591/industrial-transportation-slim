<?php
/**
 * File Name  : LoginController
* Description : php code to take input(username and password) from angular form and return a response(token) 
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */
namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use \Firebase\JWT\JWT;
use Interop\Container\ContainerInterface;
use App\api\models\LoginModel;
use App\api\services\Validator;

require_once __DIR__ . '/../../constants/EndPoints.php';

/**
 * class-name:LoginController
 * description:
 */
class LoginController{
    public $container; //variable to contain the db instance
    public $settings; //variable to contain the settings

    /**
     * a constructor to initialize the FileMaker instance and get the settings
     * @param $container
     */
    public function __construct(ContainerInterface $container){
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    /**
     * function-name:Login
     * @param $request
     * @param $request
     * description: read the input and chek for validation , for valid data return a response(token)
     */
    public function checkLogin($request, $response){
        $Email = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];

        //if email and password is emty then return an error with an error message
        if ($Email == '' || $password == '') {
            return $response->withJSON(['error' => true, 'message' => 'Email or Password is empty.'], NO_CONTENT);
        }
        //create instance of Validator
        $validator = new Validator();
        //function(ValidateEmail) call to check email validation
        $validateEmail = $validator->validateEmail($Email);        

        //if invalid email then return an error with an error message
        if (!$validateEmail) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], INVALID_CREDINTIAL);
        }
        //function(ValidateEmail) call to check password validation
        $validatePassword = $validator->validatePassword($password);

        //if password is not matched with the required pattern the return an error with an error message
        if (!$validatePassword) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], INVALID_CREDINTIAL);
        }
 
        //create instance of LoginModel
        $checkLogIn = new LoginModel();
        //function(checkLogin) call 
        $loginResponse = $checkLogIn->checkLoginModel($Email, $this->container);

        //if loginResponse is false(email is not found in the database) then return an error with an error message
        if (!$loginResponse) {
            return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], USER_NOT_FOUND);
        }

        //if the input password is matched(email is checked before and the corrosponding passwoird is matched) with db then return a response(token)
        if (password_verify($password, $loginResponse['password'])) {
            $token = JWT::encode(['id' => $loginResponse['id'], 'email' => $Email], $this->settings['jwt']['secret'], "HS256");
            return $response->withJSON(['token' => $token], SUCCESS_RESPONSE);
        }
        //if the input password is matched with db(email is found but the corrosponding passwoird is not matche with the input) then return an error message
        return $response->withJSON(['error' => true, 'message' => 'Invalid Email or Password.'], INVALID_CREDINTIAL);
    }
}
