<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

require_once "constants/EndPoints.php";

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// public api, which can access anyone without any authentication
$app->group('/public/v1', function(\Slim\App $app) {
    $app->post(USER_LOGIN_API_END_POINT,'LoginController:checkLogin');
    $app->post(USER_REGISTER_API_END_POINT,'RegisterController:register');
});

// private api, to access need a token 
$app->group('/private/v1', function(\Slim\App $app) {
    $app->post('/change-password','PasswordController:changePassword');
    $app->post('/update-user-information','UserProfileController:updateUserProfile');

    $app->post('/photo', function (Request $request, Response  $response) use ($app) {

        $directory = 'D:\industrial-transportation-slim\UserDocuments';
        $files = $request->getUploadedFiles();
        $uploadFile = $files['document'];
        if(isset($uploadFile)){
        if($uploadFile) {
            $filename = moveUploadedFile($directory, $uploadFile);
            return $response->withJSON(['message' => 'uploaded  '.$filename], 201);
        }
    }
        
    });
        
        
    function moveUploadedFile($directory, UploadedFile $uploadFile){
        $extension = pathinfo($uploadFile->getClientFilename(), 
        PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        
        return $filename;
    }
    
});
