<?php
/**
 * Manage truck details
 * created date:01/04/2019
 *
 * PHP version 5
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\controllers;

use Slim\Http\UploadedFile;
use Interop\Container\ContainerInterface;
use App\api\models\TruckModel;
use App\api\models\TruckDocumentModel;

require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ .'/../services/DecodeToken.php';

/**
 * Truck details insert and retreive
 *
 * Contain two property($container,$settings) one constructor
 * and
 */
class TruckController
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
     * Uoload truck details
     *
     * Rertrive the user id fom token. Take input and check for validate data,
     * return proper response in json format
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object return response object with JSON format
     */
    public function uploadTruckDetails($request, $response)
    {
        //get userID from token
        $id=decodeToken();
        //read input
        $body=$request->getParsedBody();

        $truckType = $body['truckType'];
        $manufacturedDate = $body['truckManufacturedDate'];
        $licenceNumber = $body['licensePlateNumber'];
        //if required inputs are emty then return an error with an error message
        if (empty($truckType) || empty($manufacturedDate) || empty($licenceNumber)) {
            return $response->withJSON(
                ['error' => true, 'message' => 'Enter the required field.'],
                NOT_ACCEPTABLE
            );
        }
        /**
         * Used to store value of valid inputs
         *
         * @var array
         */
        $requestValue = array(
            "id"=>$id,
            "truckType" => $truckType,
            "manufacturedDate" => $manufacturedDate,
            "licenceNumber" => $licenceNumber
        );
        /**
         * Used to store instance of TruckModel
         *
         * @var Object
         */
        $truckController=new TruckModel();
        $value=$truckController->uploadTruckDetails($requestValue, $this->container);
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
         */
        if (is_string($value)) {
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

    /**
     * Fetch truck details
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object return response object with JSON format
     */
    public function fetchTeuckDetails($request, $response)
    {
        //get userID from token
        $id=decodeToken();
        $requestValue = array(
            "id"=>$id
        );
        /**
         * Used to store instance of TruckModel
         *
         * @var Object
         */
        $truckController=new TruckModel();
        $value=$truckController->fetchTruckDetails($requestValue, $this->container);
    
        if (is_string($value)) {
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
        return $response->withJSON($value, SUCCESS_RESPONSE);
    }
}
