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

use App\api\models\UserModel;
use App\api\models\UserAddressLayoutModel;

require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ . '/../services/DecodeToken.php';

/**
 * class-name:UserProfileController
 * description:
 */
class UserProfileController
{
    public $container; //variable to contain the db instance
    public $settings; //variable to contain the settings

    /**
     * A constructor to initialize the FileMaker instance and get the settings
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    /**
     * Read the user information and update into the db or give response accordingly
     *
     * @param object $request
     * @param object $response
     * @return object $response in JSON format
     */
    public function updateUserProfile($request, $response)
    {
        //get the userID from token
        $id = decodeToken();
        //read the input
        $gender = $request->getParsedBody()['gender'];
        $dob = $request->getParsedBody()['dob'];
        $mobile = $request->getParsedBody()['mobileNumber'];
        $idType = $request->getParsedBody()['idType'];
        $idNumber = $request->getParsedBody()['idNumber'];
        $locality = $request->getParsedBody()['locality'];
        $landmark = $request->getParsedBody()['landmark'];
        $country = $request->getParsedBody()['country'];
        $state=$request->getParsedBody()['state'];
        $city = $request->getParsedBody()['city'];
        $postalCode = $request->getParsedBody()['postalCode'];

        //check required fields are empty or not
        if ($gender == '' || $dob == '' || $mobile == '' || $idType == ''
            || $idNumber == '' || $locality == '' || $landmark == ''
            || $country == '' || $city == '' || $postalCode == ''
        ) {
            return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        // makeing an array of valid inputs
        $requestValue = array(
            "___kp_UserId_xn" => $id,
            "Gender_xt" => $gender,
            "DateOfBirth_xd" => $dob,
            "Mobile_xn" => $mobile,
            "GovernmentIdType_xt" => $idType,
            "GovernmentIdNumber_xt" => $idNumber,
        );
        /**
         * Used to store instance of UserModel
         *
         * @var object
         */
        $instance = new UserModel();
        $value = $instance->updateProfile($requestValue, $this->container);
        if (is_string($value)) {
            $errorMessage = $this->settings['responsMessage'];
            return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        }
        $requestValue=array(
            "__kf_UserId_xn"=>$id,
            "Country_xt"=>$country,
            "State_xt"=>$state,
            "City_xt"=>$city,
            "Pincode_xn"=>$postalCode,
            "Locality_xt"=>$locality,
            "Landmark_xt"=>$landmark
        );
        /**
         * Used to store instance of UserAddressLayoutModel
         *
         * @var object
         */
        $instance = new UserAddressLayoutModel();
        $value=$instance->updateProfile($requestValue, $this->container);

        $errorMessage = $this->settings['responsMessage'];
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }

    // public function viewUserProfile($request, $response)
    // {
    //     //get the userID from token
    //     $id = decodeToken();
    //     $userProfile = new UserProfileModel();
    //     $value = $userProfile->viewUserProfile($id, $this->container);
    // }
}
