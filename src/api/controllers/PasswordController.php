<?php
/**
 * Change Passsword of a user
 * Created date : 19/03/2019
 * 
 * PHP version 5
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use Interop\Container\ContainerInterface;
use App\api\models\PasswordModel;
use App\api\services\Validator;

require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ .'/../services/DecodeToken.php';

/**
 * Contain two properties($container, $settings), one constructor and
 * one method(changePassword)
 */
class PasswordController
{
    /**
     * Used to store db information
     * 
     * @var object
     */
    public $container;
    /**
     * Used to store settings information
     * 
     * @var object
     */
    public $settings; //variable to contain the settings

    /**
     * A constructor to initialize the FileMaker instance and get the settings
     * 
     * @param object $container sontain db information
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    /**
     * Cahnge Password
     * 
     * Read the input and chek for validation , for valid input check in the db
     * and update. Return message or data according to the situation
     * 
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object           return response object with JSON format
     */
    public function changePassword($request, $response)
    {
        /**
         * Used to store userId deocded from token
         *
         * @var int
        */
        $id=decodeToken();
        
        $oldPassword=$request->getParsedBody()['oldPassword'];
        $newPassword=$request->getParsedBody()['newPassword'];
        $confirmNewPassword=$request->getParsedBody()['confirmNewPassword'];

        // Checking if any of the fields are empty
        if ($confirmNewPassword == '' || $oldPassword == '' || $newPassword=='') {
            return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        /**
         * Used to store instance of Validator
         * 
         * @var object
         */
        $validator = new Validator();
        $validatePassword = $validator->validatePassword($newPassword);

        /**
         * If password is not matched with the required pattern the return an
         * error with an error message
         */
        if (!$validatePassword) {
            return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
        }

        //check newPassword and confirmNewPassword is matched or not
        if ($newPassword!=$confirmNewPassword) {
            return $response->withJSON(['error' => true, 'message' => 'Password and Conform Password are not match.'], INVALID_CREDINTIAL);
        }
        /**
         * Used to store hold related data, which is needed to change password
         * 
         * @var array
         */
        $requestValue = array(
            "id"=>$id,
            "oldPassword" => $oldPassword,
            "password" => $newPassword
        );
        /**
         * Used to store instance of PasswordModel
         * 
         * @var object
         */
        $instance = new PasswordModel();
        $value = $instance->changePassswordModel($requestValue, $this->container);
        //get the settings for responseMessage
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }
}
