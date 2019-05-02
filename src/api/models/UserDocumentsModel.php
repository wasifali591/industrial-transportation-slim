<?php
/**
 * Manage Document
 * Created date : 03/04/2019
 * 
 * PHP version 5
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

require_once __DIR__ .'/../services/MoveFile.php';
use Slim\Http\UploadedFile;

/**
 * Contain one property($_layoutName) and two
 * method(uploadDocument, viewDocument) 
 */
class UserDocumentsModel
{
    /**
     * Used to store layou name 
     * 
     * @var string
     */
    private $_layoutName="UserDocumentLayout";
    /**
     * Upload user documents like profile picture or any government doc
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function uploadDocument($requestValue, $container)
    {
        /**
         * File path where the file will be stored
         * 
         * @var string
         */
        $directory = 'D:/industrial-transportation-slim/UserDocuments';
        $fm = $container;
        //move the file to proper directory and rename
        $fileName=moveUploadedFile($directory, $requestValue['fileName'], $requestValue['id'], $requestValue['documentType']);
        $fmquery = $fm->newAddCommand($this->_layoutName);
        $fmquery->setField("__kf_UserId_xn", $requestValue['id']);
        $fmquery->setField("Document_xr", $fileName);
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return 'USER_NOT_MATCHED';
        }
        return 'UPDATE_SUCCESSFULLY';
    }

    /**
     * View documents
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function viewDocument($requestValue, $container)
    {
        /**
         * Used to store the file path from where documents are stored
         * 
         * @var string
         */
        $directory = 'industrial-transportation-slim/UserDocuments';
        $fm = $container;
        $fmquery = $fm->newFindCommand($this->_layoutName);
        $fmquery->addFindCriterion('__kf_UserId_xn', '==' . $requestValue['id']);
        $result = $fmquery->execute();
        //if no file found then show a demo file
        if ($fm::isError($result)) {
            $response=array(
                'root'=>$directory,
                'fileName'=>"ProfilePic.jpg"
                );
            return $response;
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
        //if no file found then show a demo file
        $response=array(
            'root'=>$directory,
            'fileName'=>"ProfilePic.jpg"
            );
        return $response;
    }
}
