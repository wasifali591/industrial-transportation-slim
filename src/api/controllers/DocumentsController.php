<?php
/**
 * Manage documents
 *
 * Recieve document and type of the document(like: ProfilePic,AadharCard etc. ),
 * rename the document according to the type of the document and the document
 * belongs to which user(userID) and insert into the db, or fetch document
 * from the db acoording to the user and the type of the document
 * Created date : 27/03/2019
 * 
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\controllers;

use Slim\Http\UploadedFile;
use App\api\models\UserDocumentsModel;
use App\api\models\UserModel;
use Interop\Container\ContainerInterface;

require_once __DIR__ .'/../services/DecodeToken.php';
require_once __DIR__ .'/../../constants/StatusCode.php';

/**
 * Documents controller
 *
 * Contain two property($container,$settings) one constructor
 * and two method(uploadDocument , viewDocument)
 */
class DocumentsController
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
     * Upload Documents
     *
     * Take uploaded files, fetch userID from token, call function(uploadDocument)
     * to insert document into db and return response according to the situatuon
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     * @param array  $args     store the values send through url
     *
     * @return object           return response object with JSON format
     */
    public function uploadDocument($request, $response, $args)
    {
        /**
         * Used to store userId deocded from token
         *
         * @var int
         */
        $id=decodeToken();
        /**
         * Used to store  document type send as a argument through url
         *
         * @var string
         */
        $documentType=$args['documentType'];
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
        $uploadFile = $files['document'];

        /**
         * If the GovernmentIdType is not empty thats mean the api is receiving
         * something, which is the "ProfilePic" is not stored in database as a
         * document type. If GovernmentIdType is empty then get the idType from
         * the db
         */
        if (empty($documentType)) {
            $requestValue=array(
                "___kp_UserId_xn"=>$id
            );
            /**
            * Used to store instance of DocumnetsModel
            *
            * @var object
            */
            $instance=new UserModel();
            $value=$instance->searchRecord($requestValue, $this->container);
            /**
             * If the value of the variable $value is string, thats means the
             * variable hold some kind of error message or information message
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
            $documentType=$value['GovernmentIdType_xt'];

        }
        /**
         * Used to store userID,document type and uploaded filename
         *
         * @var array
         */
        $requestValue = array(
            "id" => $id,
            "documentType"=>$documentType,
            "fileName" => $uploadFile,
        );
        /**
         * Used to store instance of UserDocumnetsModel
         *
         * @var object
         */
        $instance=new UserDocumentsModel();
        /**
         * Used to store the return value of the function uploadDocumnet
         *
         * @var string or
         * @var array
         */
        $value=$instance->uploadDocument($requestValue, $this->container);
        /**
         * Used to store responseMessage array from settings
         *
         * @var array
         */
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }

    /**
     * Fetch document from db
     *
     * Fetch documnet depending on userID and the type of the document.
     * generate required file name from userID and document type
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     * @param array  $args     store the values send through url
     * 
     * @return object return response object with JSON format
     */
    public function viewDocument($request, $response, $args)
    {
        /**
         * Used to store userId deocded from token
         *
         * @var int
         */
        $id=decodeToken();
        /**
         * Used to store value send through url
         *
         * @var string
         */
        $documentType = $args['documentType'];
        /**
         * Used to store the file name which is generated after concatination of
         * userID and type of document and one underscore between them
         *
         * @var array
         */
        $fileName=$id.'_'.$documentType;
        /**
         * Used to store userID and file name
         *
         * @var array
         */
        $requestValue = array(
            "id" => $id,
            "fileName" => $fileName,
        );
        /**
         * Used to store instance of DocumnetsModel
         *
         * @var object
         */
        $instance=new UserDocumentsModel();
        $value=$instance->viewDocument($requestValue, $this->container);
        $errorMessage=$this->settings['responsMessage'];
        /**
         * If the value of the variable $value is string, thats means the
         * variable hold some kind of error message or information message
         */
        if (is_string($value)) {
            return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        } else {
            $documentPath= $value['root'].'/'.$value['fileName'];
            return $response->withJSON(['document'=>$documentPath], SUCCESS_RESPONSE);
        }
    }
}
