<?php
/**
 * File Name  : UserProfileController
* Description : user profile view and update 
* Created date : 25/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */
namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use \Firebase\JWT\JWT;
use Interop\Container\ContainerInterface;
use Slim\Http\UploadedFile;

use App\api\models\UserProfileModel;

require_once __DIR__ . '/../../constants/StatusCode.php';

/**
 * class-name:UserProfileController
 * description:
 */
class UserProfileController
{
    public $container; //variable to contain the db instance
    public $settings; //variable to contain the settings
    public $directory = 'D:\industrial-transportation-slim\UserDocuments'; //path of the directory where all the documents are sgtored

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
     * function-name: updateUserProfile
     * @param $request
     * @param $response
     * description: read the user information and give response accordingly 
     */
    public function updateUserProfile($request, $response)
    {
        //read the token from header and from the token decode the id of the user
        $headers = apache_request_headers();
        $string = $headers['Authorization'];
        $str_arr = preg_split("/\ /", $string);
        $decoded = JWT::decode($str_arr[1], "truckage", array('HS256'));
        $decoded_array = (array)$decoded;
        $id = $decoded_array['id'];

        //read the input
        $gender = $request->getParsedBody()['gender'];
        $dob = $request->getParsedBody()['dob'];
        $mobile = $request->getParsedBody()['mobileNumber'];
        $idType = $request->getParsedBody()['idType'];
        $idNumber = $request->getParsedBody()['idNumber'];
        // $files = $request->getUploadedFiles();
        // $uploadFile = $files['document'];
        $locality = $request->getParsedBody()['locality'];
        $landmark = $request->getParsedBody()['landmark'];
        $country = $request->getParsedBody()['country'];
        $city = $request->getParsedBody()['city'];
        $postalCode = $request->getParsedBody()['postalCode'];

        //check required fields are empty or not
        if ($gender == '' || $dob == '' || $mobile == '' || $idType == '' || $idNumber == '' || //$uploadFile == ''||
            $locality == '' || $landmark == '' || $country == '' || $city == '' || $postalCode == '') { 
                return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        // makeing an array of valid inputs
        $requestValue = array(
            "id"=>$id,
            "gender"=>$gender,
            "dob"=>$dob,
            "mobile"=>$mobile,
			"idType" => $idType,
            "idNumber"=>$idNumber,
            //"uploadFile"=>$uploadFile,
            "locality"=>$locality,
            "landmark"=>$landmark,
            "country"=>$country,
            "city"=>$city,
            "postalCode"=>$postalCode
        );
         //creating an instance of PasswordModel
         $userProfileModel = new UserProfileModel();
         $value = $userProfileModel->updateUserProfileModel($requestValue,$this->container);

         if($value=="SERVER_ERROR"){
             return $response->withJSON(['error' => true, 'message' => 'Internal server error'], INTERNAL_SERVER_ERROR);
         }

         if($value=="USER_NOT_MATCHED"){
            return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], USER_NOT_FOUND);
        }

        if($value=="UPDATED"){
            return $response->withJSON(['message' => 'Yor profile is successfully updated.'], SUCCESS_RESPONSE);
        }
        
    }

    
    public function uploadImage($imageName, $id)
    {   
        if ($uploadFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($this->directory, $uploadFile, $id,$idType);
            return $response->withJSON(['message' => 'uploaded'], 201);
        }


        $fmquery = $this->container->newAddCommand("UserDocumentLayout");
        $fmquery->setField("__kf_UserId_xn", $id);
        $fmquery->setField("Document_xr", $imageName);
        $result = $fmquery->execute();
        return $result;
    }
}
