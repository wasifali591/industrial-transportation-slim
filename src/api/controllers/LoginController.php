<?php

/**
 * Login
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
use App\api\models\UserCredentialsModel;
use App\api\models\UserModel;
use App\api\services\Validator;

require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * Login controller
 *
 * Contain two property($container,$settings) one constructor
 * and one method(login)
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
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object return response object with JSON format
     */
    public function login($request, $response)
    {
        //read input
        $body=$request->getParsedBody();
        
        $email = $body['username'];
        $password = $body['password'];

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
        $validateEmail = $validator->validateEmail($email);

        //if invalid email then return an error with an error message
        if (!$validateEmail) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], INVALID_CREDINTIAL);
        }
        $validatePassword = $validator->validatePassword($password);

        /**
         * If password is not matched with the required pattern the
         * return an error with an error message
         */
        if (!$validatePassword) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], INVALID_CREDINTIAL);
        }
        /**
         * Used to store request value according to the operation
         *
         * @var array
         */
        $requestValue=array(
            'Email_xt'=>$email
        );
        $instance=new UserModel();
        $value=$instance->searchRecord($requestValue, $this->container);
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
         */
        if (is_string($value)) {
            /**
             * Used to store responseMessage array from settings
             *
             * @var array
            */
            $responseMessage=$this->settings['responsMessage'];
            return $response->withJSON(['error' => $responseMessage[$value]['error'], 'message' => $responseMessage[$value]['message']], $responseMessage[$value]['statusCode']);
        }
        $userInformation=$value[0];
        
        $requestValue=array(
            '__kf_UserId_xn'=>$userInformation['___kp_UserId_xn'],
            'Flag_xt'=>"active"
        );
        $login = new UserCredentialsModel();
        //function(login) call
        $value = $login->login($requestValue, $this->container);
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
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
        $userCredential=$value[0];
       
        /**
         * If the input password is matched(email is checked before and the
         * corrosponding passwoird is matched) with db then return a response(token)
        */
        if (password_verify($password, $userCredential['Password_xt'])) {
            $token = JWT::encode(
                ['id' => $userInformation['___kp_UserId_xn'], $userInformation['Email_xt']],
                $this->settings['jwt']['secret'],
                "HS256"
            );
            return $response->withJSON(
                ['token' => $token, 'userInformation' => $userInformation],
                SUCCESS_RESPONSE
            );
        }
        return $response->withJSON(['error'=>false,'message'=>"Password is incorrect."], INVALID_USER_PASS);
    }
}
