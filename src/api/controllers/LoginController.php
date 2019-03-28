<?php

/**
 * Manage login
 *
 * Receive input(username and password)  and return a token if the credentials
 * are match or return appropriate message
 * Created date : 19/03/2019
 *
 * PHP version 5
 *
 * @package JWT
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
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
 * Login controller
 * 
 * Contain two property($container,$settings) one constructor
 * and one method(checkLogin)
 */
class LoginController
{
    /**
     * Used to contain db instance
     *
     * @var Object
     */
    public $container;

    /**
     * Used to contain settings
     *
     * @var Object
     */
    public $settings;

    /**
     *  Initialize the FileMaker instance and get the settings
     *
     * @param object $container contain information related to db
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    /**
     * Login checking with proper credentials
     * 
     * Take input and check for validate data, call function to check username
     * and corosponding password is present in the db or not, return token or
     * related message
     *
     * @param  object $request  represents the current HTTP request received
     *                          by the web server
     * @param  object $response represents the current HTTP response to be
     *                          returned to the client.
     * @return object return response object with JSON format
     */
    public function checkLogin($request, $response)
    {
        $email = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];

        //if email and password is emty then return an error with an error message
        if (empty($email) || empty($password)) {
            return $response->withJSON(['error' => true, 'message' => 'Email or Password is empty.'], NO_CONTENT);
        }
        /**
         * Used to store instance of Validator
         * 
         * @var Object
         */
        $validator = new Validator();

        //function(ValidateEmail) call to check email validation
        $validateEmail = $validator->validateEmail($email);

        //if invalid email then return an error with an error message
        if (!$validateEmail) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], INVALID_CREDINTIAL);
        }
        //function(ValidateEmail) call to check password validation
        $validatePassword = $validator->validatePassword($password);

        /**
         * If password is not matched with the required pattern the
         * return an error with an error message 
         */
        if (!$validatePassword) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], INVALID_CREDINTIAL);
        }

        /**
         * Used to store instance of LoginModel
         * 
         * @var Object
         */
        $checkLogIn = new LoginModel();
        //function(checkLogin) call
        $value = $checkLogIn->checkLoginModel($email, $this->container);

        /**
         * If the return value of the function is string then return response with
         * corosponding message of the val;ue
         */
        if (is_string($value)) {
            //get the settings for responseMessage
            $errorMessage = $this->settings['responsMessage'];
            return $response->withJSON(
                ['error' => $errorMessage[$value]['error'],
                'message' => $errorMessage[$value]['message']],
                $errorMessage[$value]['statusCode']
            );
        }
        /**
         * If the input password is matched(email is checked before and the
         * corrosponding passwoird is matched) with db then return a response(token)
        */
        if (password_verify($password, $value['password'])) {
            $token = JWT::encode(
                ['id' => $value['id'], 'email' => $email],
                $this->settings['jwt']['secret'], "HS256"
            );
            return $response->withJSON(
                ['token' => $token, 'email' => $email,
                'firstName' => $value['firstName'],
                'lastName' => $value['lastName']], SUCCESS_RESPONSE
            );
        }
    }
}
