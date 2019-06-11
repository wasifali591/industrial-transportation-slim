<?php
/**
 * Insert and retrieve documents related to Truck
 * Created date : 02/04/2019
 * 
 * PHP version 7
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Contain one property($_layoutName) and one method(uploadTruckDocument)
 */
class TruckDocumentModel
{
    private $_layoutName="TruckDocumentLayout";
    /**
     * Insert the truck document into the db
     *
     * @param int    $id           hold the truck id
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     * 
     * @return multiple types of value according to the situation
     */
    public function uploadTruckDocument($id, $requestValue, $container)
    {
        /**
         * Used to store the file path where the documents will be stored
         * 
         * @var string
         */
        $directory='industrial-transportation-slim/UserDocuments';
        /**
         *
         * @var object
         */
        $fm = $container;
        $fieldsName=array(
            "RegistrationCertificate_xr"=>$requestValue['registrationCertificate'],
            "InsurancePaper_xr"=>$requestValue['insurancePaper'],
            "PUCCertificate_xr"=>$requestValue['polutionCertificate'],
        );
        foreach ($fieldsName as $field=>$value) {
            var_dump($field);
            exit();
            //move the file to proper directory and rename
            $fileName=moveUploadedFile($directory, $value, $id, $field);
            echo $fileName;
            exit();
            $fmquery = $fm->newAddCommand($this->_layoutName);
            $fmquery->setField("__kf_UserId_xn", $id);
            $fmquery->setField("Document_xr", $fileName);
            $result = $fmquery->execute();
 
            if ($fm::isError($result)) {
                return 'USER_NOT_MATCHED';
            }
            return 'UPDATE_SUCCESSFULLY';
        }
    }
}
