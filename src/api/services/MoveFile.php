<?php
/**
 * Generate HashCode of a given password
 * Created date : 25/03/2019
 * 
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

use Slim\Http\UploadedFile;

/**
 * Move uploaded file to a selective directory and rename
 * the file to acoordintg to the file type
 * 
 * @param string $directory    path of the directory where the file will store
 * @param object $uploadedFile the file we want to store 
 * @param int    $id           user id of the user
 * @param string $idType       which type of documents are want to upload
 *
 * @return string $name        name of the file 
 */
function moveUploadedFile($directory, UploadedFile  $uploadedFile, $id, $idType)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = $idType;
    $name = $id .'_'. $basename . '.' . $extension;
    if (file_exists($directory.'/'.$name)) {
        unlink($directory.'/'.$name);
    }
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $name);
    return $name;
}
