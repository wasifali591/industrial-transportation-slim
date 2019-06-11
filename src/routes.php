<?php
/**
 * Routes
 * Created date : 19/03/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
use Slim\Http\Request;
use Slim\Http\Response;

require_once "constants/EndPoints.php";

$app->get(
    '/[{name}]',
    function (Request $request, Response $response, array $args) {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");

        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    }
);

// public api, which can access anyone without any authentication
$app->group(
    '/public/v1',
    function (\Slim\App $app) {
        $app->post(USER_LOGIN_API_END_POINT, 'LoginController:login');
        $app->post(USER_REGISTER_API_END_POINT, 'RegisterController:register');
    }
);

// private api, to access need a token
$app->group(
    '/private/v1',
    function (\Slim\App $app) {
        $app->post(CHANGE_PASSWORD_API_END_POINT, 'PasswordController:changePassword');
        $app->post(UPDATE_USER_INFORMATION_API_END_POINT, 'UserProfileController:updateUserProfile');
        $app->post(UPLOAD_DOCUMENT_API_END_POINT, 'DocumentsController:uploadDocument');
        $app->post(UPDATE_TRUCK_INFORMATION_API_END_POINT, 'TruckController:uploadTruckDetails');

        $app->get(VIEW_DOCUMENT_API_END_POINT, 'DocumentsController:viewDocument');
        $app->get(VIEW_TRUCK_DETAILS_API_END_POINT, 'TruckController:fetchTeuckDetails');
        $app->get(VIEW_USER_INFORMATION_API_END_POINT, 'UserProfileController:viewUserProfile');
    }
);
