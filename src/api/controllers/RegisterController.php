<?php
/**
 * Register Controller
 *
 * Receive all the input from client side, perform registration operarion
 * and return response according to the situation
 * Created date : 19/03/2019
 *
 * PHP version 5
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
use App\api\services\Validator;
use App\api\models\UserModel;

require_once __DIR__ . '/../../constants/EndPoints.php';
require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ .'/../services/HashCode.php';

/**
 * Register Controller
 *
 * Contain two property($container, $settings) one constructor and
 * one method(register)
 */
class RegisterController
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
     * Method for register
     *
     * Read input and check if the inputs are empty or not, if not empty then
     * check for proper validation, if validate data are present then perform
     * the rest of registration. Rerturn message according to the situation
     *
     * @param  object $request  represents the current HTTP request received
     *                          by the web server
     * @param  object $response represents the current HTTP response to be
     *                          returned to the client.
     * 
     * @return object
     */
    public function register(Request $request, Response $response)
    {
        // Receiving values from client side and assigning it to a variable
        $firstName = $request->getParsedBody()['firstName'];
        $lastName = $request->getParsedBody()['lastName'];
        $Email = $request->getParsedBody()['email'];
        $userType = $request->getParsedBody()['userType'];
        $password = $request->getParsedBody()['password'];
        $confirmPassword = $request->getParsedBody()['confirmPassword'];

        // Checking if any of the fields are empty
        if (empty($firstName) || empty($lastName) || empty($Email)
            || empty($userType) || empty($password) || empty($confirmPassword)
        ) {
            return $response->withJSON(
                ['error' => true, 'message' => 'Enter the required field.'],
                NOT_ACCEPTABLE
            );
        }
        /**
         * Used to store instance of Validator
         *
         * @var object
         */
        $validator = new Validator();
        $validateEmail = $validator->validateEmail($Email);

        //if invalid email then return an error with an error message
        if (!$validateEmail) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
        }
        $validatePassword = $validator->validatePassword($password);

        /**
         *If password is not matched with the required pattern the return an
         *error with an error message
         */
        if (!$validatePassword) {
            return $response->withJSON(
                ['error' => true, 'message' => 'Enter valid Password.'],
                UNAUTHORIZED_USER
            );
        }

        //if password and confirm password are not match return an error message
        if ($password !== $confirmPassword) {
            return $response->withJSON(['error' => true, 'message' => 'Password and Conform Password are not match.'], INVALID_CREDINTIAL);
        }
        /**
         * Used to store the hash code of $password genated by a function(hashCode)
         *
         * @var string
         */
        $hashCode=hashCode($password);
        /**
         * Used to store value of valid inputs
         *
         * @var array
         */
        $requestValue = array(
            "userType" => $userType,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "email" => $Email,
            "password" => $hashCode
        );
        /**
         * Used to store instance of UserModel
         *
         * @var object
         */
        $registration = new UserModel();
        $value = $registration->registration($requestValue, $this->container);
        /**
         * Used to store responseMessage setting
         *
         * @var array
         */
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(
            ['error' => $errorMessage[$value]['error'],
             'message' => $errorMessage[$value]['message']],
            $errorMessage[$value]['statusCode']
        );
    }
}
