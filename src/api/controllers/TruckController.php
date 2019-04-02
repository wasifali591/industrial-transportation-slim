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

require_once __DIR__ . '/../../constants/EndPoints.php';
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
        $truckType = $request->getParsedBody()['truckType'];
        $manufacturedDate = $request->getParsedBody()['truckManufacturedDate'];
        $licenceNumber = $request->getParsedBody()['licensePlateNumber'];
        /**
         * Used to store getUploadedFiles function reference
         *
         * @var object
         */
        $files = $request->getUploadedFiles();
        /**
         * Used to store property of the uploaded document
         *
         * @var array
         */
        $registrationCertificate = $files['registrationCertificate'];
        $insurancePaper = $files['insuranceDocument'];
        $polutionCertificate = $files['pollutionDocument'];
        //if required inputs are emty then return an error with an error message
        if (empty($truckType) || empty($manufacturedDate) || empty($licenceNumber)) {
            return $response->withJSON(
                ['error' => true, 'message' => 'Enter the required field.'],
                NO_CONTENT
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
        return $response->withJSON(
            ['error' => false,
            'message' => "Truck added"],
            201
        );
        // $truckId=$value['___kp_TruckId_xn'];
        // $requestValue = array(
        //     "registrationCertificate" => $registrationCertificate,
        //     "insurancePaper" => $insurancePaper,
        //     "polutionCertificate" => $polutionCertificate
        // );
        // /**
        //  * Used to store instance of TruckDocumnetsModel
        //  *
        //  * @var Object
        //  */
        // $truckController=new TruckDocumentModel();
        // $value=$truckController->uploadTruckDocument($truckId, $requestValue, $this->container);
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
        return $response->withJSON($value, 200);
    }
}
