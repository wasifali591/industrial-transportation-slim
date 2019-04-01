<?php
/**
 * CRUD operation in file maker
 *
 * Genarelized function for CRUD operation. functions take LayoutName
 * and Field name and db instance as a input and perform operation
 * Created date : 29/03/2019
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\services;

require_once __DIR__ .'/../../constants/StatusCode.php';

/**
 *
 */
class CRUDOperation
{
    /**
     * Create new record in db
     *
     * @param  string $layoutName on which want to perform the opeartion
     * @param  array  $fieldsName hold the field name
     * @param  object $fm         database instance
     * @return string
     * @return array
     */
    public function createRecord($layoutName, $fieldsName, $fm)
    {
        $response=array();
        $fmquery = $fm->newAddCommand($layoutName);

        //For every key(Field Name) set their value
        while (list($key, $val) = each($fieldsName)) {
            $fmquery->setField($key, $val);
        }
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "SERVER_ERROR";
        }
        $recs = $result->getRecords();
        $record = $recs[0];
        $field=$record->getFields();
        foreach ($field as $field_name) {
            $response[$field_name] = $record->getField($field_name);
        }        
        return $response;
    }

    /**
     * Find record into db
     *
     * @param  string $layoutName on which want to perform the opeartion
     * @param  array  $fieldsName hold the field name
     * @param  object $fm         database instance
     * @return string
     * @return array
     */
    public function findRecord($layoutName, $fieldsName, $fm)
    {
        $count=count($fieldsName);
        $fmquery = $fm->newFindCommand($layoutName);

        if ($count===1) {
            $field=each($fieldsName);
            $fmquery->addFindCriterion($field['key'], '==' . $field['value']);
        } else {
            $fmquery->setLogicalOperator('FILEMAKER_FIND_AND');

            while (list($key, $val) = each($fieldsName)) {
                $fmquery->addFindCriterion($key, $val);
            }
        }
        $result = $fmquery->execute();
        
        if ($fm::isError($result)) {
            return false;
        }
        return true;
    }
}
