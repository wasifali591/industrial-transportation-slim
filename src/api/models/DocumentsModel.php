<?php

namespace App\api\models;

require_once __DIR__ .'/../services/MoveFile.php';
use Slim\Http\UploadedFile;

class DocumentsModel
{
    public $directory = 'D:\industrial-transportation-slim\UserDocuments'; //path of the directory where all the documents are sgtored
    public function uploadDocument($requestValue, $container)
    {
        $fm = $container;
        //find the userID in db(USerLayout)
        $fmquery = $fm->newFindCommand("UserLayout");
        $fmquery->addFindCriterion('___kp_UserId_xn', '==' . $requestValue['id']);
        $result = $fmquery->execute();

        //if userID not found return false
        if ($fm::isError($result)) {
            return 'USER_NOT_MATCHED';
        }
        
        $GovernmentIdType=$requestValue['documentType'];
        
        /**
         * If the GovernmentIdType is not empty thats mean the api is receiving
         * something, which is the "ProfilePic" is not stored in database as a
         * document type. If GovernmentIdType isempty then get the idType from
         * the db 
         */
        if ($GovernmentIdType==='') {
            $records = $result->getRecords();
            $record = $records[0];
            $GovernmentIdType = $record->getField('GovernmentIdType_xt');
        }
        //move the file to proper directory and rename
        $fileName=moveUploadedFile($this->directory, $requestValue['fileName'], $requestValue['id'], $GovernmentIdType);
        $fmquery = $fm->newAddCommand("UserDocumentLayout");
        $fmquery->setField("__kf_UserId_xn", $requestValue['id']);
        $fmquery->setField("Document_xr", $fileName);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return 'USER_NOT_MATCHED';
        }
        return 'UPDATE_SUCCESSFULLY';
    }

    public function viewDocument($requestValue, $container)
    {
        $directory='industrial-transportation-slim/UserDocuments';
        $fm = $container;
        $fmquery = $fm->newFindCommand("UserDocumentLayout");
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $requestValue['id']);
        $result = $fmquery->execute();
        if ($fm::isError($result)) {
            return "USER_NOT_MATCHED";
        }
        $records = $result->getRecords();

        //find the document in db
        foreach ($records as $record) {
            $document=$record->getField('Document_xr');
            $str_arr = preg_split("/\//", $document);
            $fileName=preg_split("/\./", $str_arr[4]);
            
            if ($requestValue['fileName']== $fileName[0]) {
                $fileExtension=preg_split("/\?/", $fileName[1]);
                $name=$fileName[0].'.'.$fileExtension[0];
                $response=array(
                        'root'=>$directory,
                        'fileName'=>$name
                        );
                return $response;
            }
        }
        return "DOCUMENT_NOT_FOUND";
    }
}
