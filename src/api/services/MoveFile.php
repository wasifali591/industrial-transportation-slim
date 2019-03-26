<?php
/**
 * File Name  : HashCode
* Description : generate hashcode
* Created date : 25/03/2019
* Author  : Md Wasif Ali
* Comments : 
 */

use Slim\Http\UploadedFile;

/**
 * function-name:moveUploadedFile
 * @param $directory
 * @param $uploadedFile
 * @param $id
 * @param $idType
 * description: move the uploaded file to destination folder, and rename the file 
*/

function moveUploadedFile($directory, UploadedFile  $uploadedFile, $id, $idType)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = $idType;
    $name = $id . $basename . '.' . $extension;
    $result = $this->uploadImage($name, $id);
    if ($result) {
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $name);
        return $name;
    }
    return false;
}

