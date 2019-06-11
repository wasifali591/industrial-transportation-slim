<?php
/**
 * User Profile Controller
 *
 * User profile view and update
 * Created date : 25/03/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use \Firebase\JWT\JWT;
use Interop\Container\ContainerInterface;
use Slim\Http\UploadedFile;

use App\api\models\UserModel;
use App\api\models\UserAddressModel;

require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ . '/../services/DecodeToken.php';

/**
 * Contain 2 properties($container,$settings), one constructor and
 * two methods(updateUserProfile,viewUserProfile)
 */
class UserProfileController
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
     * Read the user information and update into the db. return response accordingly
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object           return response object with JSON format
     */
    public function updateUserProfile($request, $response)
    {
        /**
         * Used to store userId deocded from token
         *
         * @var int
         */
        $id = decodeToken();
        //read the input
        $body=$request->getParsedBody();

        $gender = $body['gender'];
        $mobile=$body['mobileNumber'];
        $idType=$body['idType'];
        $idNumber=$body['idNumber'];
        $locality=$body['locality'];
        $landmark=$body['landmark'];
        $country=$body['country'];
        $city=$body['city'];
        // $state=$body['state'];
        $dob=$body['dob'];
        $postalCode=$body['postalCode'];
        //check required fields are empty or not
        if (empty($gender) || empty($dob) || empty($mobile) || empty($idType)
            || empty($idNumber) || empty($locality) || empty($landmark)
            || empty($country) || empty($city) || empty($postalCode)
        ) {
            return $response->withJSON(['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE);
        }
        /**
         * Used to store valid inputs, which want to update into db
         *
         * @var array
         */
        $requestValue = array(
            "___kp_UserId_xn" => $id,
            "Gender_xt" => $gender,
            "DateOfBirth_xd" => $dob,
            "Mobile_xn" => $mobile,
            "GovernmentIdType_xt" => $idType,
            "GovernmentIdNumber_xt" => $idNumber
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
            return $response->withJSON(['test'=>"test",'error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        }
        /**
         * Used to store address details, which want to update into db
         *
         * @var array
         */
        $requestValue=array(
            "__kf_UserId_xn"=>$id,
            "Country_xt"=>$country,
            // "State_xt"=>$state,
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
        $instance = new UserAddressModel();
        $value=$instance->updateProfile($requestValue, $this->container);

        $errorMessage = $this->settings['responsMessage'];
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }

    /**
     * View user information
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object           return response object with JSON format
     */
    public function viewUserProfile($request, $response)
    {
        /**
         * Used to store userId deocded from token
         *
         * @var int
         */
        $id = decodeToken();
        /**
         * Used to store valid inputs, which want to update into db
         *
         * @var array
         */
        $requestValue = array(
            "___kp_UserId_xn" => $id
        );
        /**
         * Used to store instance of UserModel
         */
        $instance = new UserModel();
        $value = $instance->viewProfile($requestValue, $this->container);
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
         */
        if (is_string($value)) {
            $errorMessage = $this->settings['responsMessage'];
            return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        }
        $userInformation=$value[0];
        $requestValue = array(
            "__kf_UserId_xn" => $id
        );
        /**
         * Used to store instance of UserAddressModel
         */
        $instance = new UserAddressModel();
        $value = $instance->viewProfile($requestValue, $this->container);
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
         */
        if (is_string($value)) {
            $errorMessage = $this->settings['responsMessage'];
            return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        }
        $userAddress=$value[0];
        return $response->withJSON(['Information'=>$userInformation,'Address'=>$userAddress], SUCCESS_RESPONSE);
    }
}
