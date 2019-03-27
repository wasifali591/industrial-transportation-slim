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
 * @param string $directory path of the directory where the file will store
 * @param UploadedFile $uploadedFile the file 
 * @param number $id user id of the user
 * @param string $idType which type of documents are want to upload
 * description: move the uploaded file to destination folder, and rename the file 
*/

function moveUploadedFile($directory, UploadedFile  $uploadedFile, $id, $idType)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = $idType;
    $name = $id .'_'. $basename . '.' . $extension;
    if (file_exists($directory.'/'.$name)){
         unlink($directory.'/'.$name);
    }
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $name);
    return $name;
}

