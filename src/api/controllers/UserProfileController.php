<?php
/**
 * File Name  : UserProfileController
* Description : php code to take input(username and password) from angular form and return a response(token) 
* Created date : 25/03/2019
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
use Slim\Http\UploadedFile;

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
        $document = $request->getParsedBody()['document'];
        $document_arr = preg_split("/\//", $document);
        // $files = $request->getUploadedFiles();
        // $uploadFile = $files['document'];
        print_r($document_arr);
        $arr_count=count($document_arr);
        $uploadFile= $document_arr[$arr_count-1];
        $locality = $request->getParsedBody()['locality'];
        $landmark = $request->getParsedBody()['landmark'];
        $country = $request->getParsedBody()['country'];
        $city = $request->getParsedBody()['city'];
        $postalCode = $request->getParsedBody()['postalCode'];
        $filename = $this->moveUploadedFile($this->directory, $uploadFile, $id);
        //print_r($uploadFile);
        //  
        //check required fields are empty or not
        if ($gender == '' || $dob == '' || $mobile == '' || $idType == '' || $idNumber == '' || $document == ''||
            $locality == '' || $landmark == '' || $country == '' || $city == '' || $postalCode == '') { 
                return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        return $response->withJSON(['message' => 'checked'], 200);
        //makeing an array of valid inputs
        // $requestValue = array(
        //     "id"=>$id,
        //     "gender"=>$gender,
        //     "dob"=>$dob,
        //     "mobile"=>$mobile,
		// 	"idType" => $idType,
        //     "idNumber"=>$idNumber,
        //     "uploadFile"=>$uploadFile,
        //     "locality"=>$locality,
        //     "landmark"=>$landmark,
        //     "country"=>$country,
        //     "city"=>$city,
        //     "pinCode"=>$pinCode
		// );
        // if ($uploadFile->getError() === UPLOAD_ERR_OK) {
            // $filename = $this->moveUploadedFile($this->directory, $uploadFile, $id);
        //     return $response->withJSON(['message' => 'uploaded'], 201);
        // }
    }
    //UploadedFile->getClientFilename()
    public function moveUploadedFile($directory,  $uploadedFile, $id)
    {
        $extension = pathinfo($uploadedFile, PATHINFO_EXTENSION);
        //$filename = $uploadedFile->getClientFilename();
        //$basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        //$filename = sprintf('%s.%0.8s', $basename, $extension);
        $basename = "voterID";
        $name = $id . $basename . '.' . $extension;
        // echo $name;
        // exit();
        // $this->container->
        // $result = $this->uploadImage($name, $id);
        // if ($result) {
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $name);
            return $name;
        // }
        // return false;
    }
    public function uploadImage($imageName, $id)
    {
        $fmquery = $this->container->newAddCommand("UserDocumentLayout");
        $fmquery->setField("__kf_UserId_xn", $id);
        $fmquery->setField("Document_xr", $imageName);
        $result = $fmquery->execute();
        return $result;
    }

    public function updateUserProfileModel(array $requestValue, $container){

    }
}
