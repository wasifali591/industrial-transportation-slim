<?php
/**
 * Insert and retrieve data related to Truck
 * Created date : 01/04/2019
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Contain
 */
class TruckModel
{
    private $_layoutName="TruckLayout";
    /**
     * Insert the truck details into the db
     *
     * @param  array  $requestValue hold the value to be insert into db
     * @param  object $container    hold the db instance
     * @return
     */
    public function uploadTruckDetails($requestValue, $container)
    {
        /**
         * Used to store db information
         *
         * @var object
         */
        $fm = $container;
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $crud=new CRUDOperation();
        $fieldsName=array(
            "LicenceNumber_xt"=>$requestValue['licenceNumber']
        );
        $result=$crud->findRecord($this->_layoutName, $fieldsName, $fm);
        //if same truck is not present in the db then insert the input into db
        if (!$result) {
            $fieldsName=array(
            "__fk_UserId_xn"=>$requestValue['id'],
            "TruckType_xt"=>$requestValue['truckType'],
            "ManufacturedDate_xd"=>$requestValue['manufacturedDate'],
            "LicenceNumber_xt"=>$requestValue['licenceNumber']
            );
            $result=$crud->createRecord("TruckLayout", $fieldsName, $fm);
            return $result;
        }
        return "TRUCK_ALREADY_REGISTERED";
    }
}
