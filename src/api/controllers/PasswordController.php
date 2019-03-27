<?php
/**
 * File Name  : PasswordController
* Description : php code to  change password in db
* Created date : 19/03/2019
* Author  : Md Wasif Ali
* Comments : 
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
 * class-name:PasswordController
 * description:
 */
class PasswordController
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
     * function-name:ChangePassword
     * @param $request
     * @param $request
     * description: read the input and chek for validation , for valid input check in the db and update
     */
    public function changePassword($request, $response){
        //get userID from token    
        $id=decodeToken();
        //read input
        $oldPassword=$request->getParsedBody()['oldPassword'];
        $newPassword=$request->getParsedBody()['newPassword'];
        $confirmNewPassword=$request->getParsedBody()['confirmNewPassword'];

        // Checking if any of the fields are empty
        if ($confirmNewPassword == '' || $oldPassword == '' || $newPassword=='') {
        	return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        //create instance of Validator
		$validator = new Validator();
        //function(ValidatePassword) call to check password validation
        $validatePassword = $validator->validatePassword($newPassword);

		//if password is not matched with the required pattern the return an error with an error message
		if (!$validatePassword) {
			return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
		}

        //check newPassword and confirmNewPassword is matched or not
        if($newPassword!=$confirmNewPassword){
            return $response->withJSON(['error' => true, 'message' => 'Password and Conform Password are not match.'], INVALID_CREDINTIAL);
        }
        //$requestValue is an array hold related data, which is needed to change password 
        $requestValue = array(
            "id"=>$id,
        	"oldPassword" => $oldPassword,
        	"password" => $newPassword
        );
        //creating an instance of PasswordModel
        $passwordModel = new PasswordModel();
        $value = $passwordModel->changePassswordModel($requestValue,$this->container);
        //get the settings for responseMessage
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }
}
