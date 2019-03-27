<?php
namespace App\api\controllers;

use Slim\Http\UploadedFile;
use App\api\models\DocumentsModel;
use Interop\Container\ContainerInterface;

require_once __DIR__ .'/../services/DecodeToken.php';

class DocumentsController{
    public $container; //variable to contain the db instance
    public $settings; //variable to contain the settings

    /**
     * a constructor to initialize the FileMaker instance and get the settings
     * @param $container
     */
    public function __construct(ContainerInterface $container){
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }
    public function uploadDocument($request,$response,$args){
        //get the userID from token
        $id=decodeToken();
        // $documentType = $request->getParsedBody()['documentType'];
        $documentType=$args['documentType'];
        $files = $request->getUploadedFiles();
        $uploadFile = $files['document'];
        $requestValue = array(
            "id" => $id,
            "documentType"=>$documentType,
			"fileName" => $uploadFile,
        );
        //create instance of DocumnetsModel
        $document=new DocumentsModel();
        $value=$document->uploadDocument($requestValue,$this->container);
        //get the settings for responseMessage
        $errorMessage=$this->settings['responsMessage'];
        
        return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
    }


    public function viewDocument($request,$response,$args){
        //get the userID from token
        $id=decodeToken();
        $documentType = $args['documentType'];
        $fileName=$id.'_'.$documentType;
        $requestValue = array(
			"id" => $id,
			"fileName" => $fileName,
        );
        //create instance of DocumnetsModel
        $document=new DocumentsModel();
        $value=$document->viewDocument($requestValue,$this->container);
        $errorMessage=$this->settings['responsMessage'];
        if(is_string($value)){
            return $response->withJSON(['error' => $errorMessage[$value]['error'], 'message' => $errorMessage[$value]['message']], $errorMessage[$value]['statusCode']);
        }else{
            $documentPath= $value['root'].'/'.$value['fileName'];
            return $response->withJSON(['document'=>$documentPath],200);
        }

    }

    
}