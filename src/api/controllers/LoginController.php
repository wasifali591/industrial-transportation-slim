<?php
namespace App\api\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use \Firebase\JWT\JWT;
use Interop\Container\ContainerInterface;
use App\api\models\LoginModel;
use App\api\services\Validator;

require_once __DIR__ . '/../../constants/EndPoints.php';

class LoginController{
    public $container;
    public $settings;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    public function home($request, $response)
    {
        $Email = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];

        if ($Email == '' || $password == '') {
            return $response->withJSON(['error' => true, 'message' => 'Email or Password is empty.'], NO_CONTENT);
        }
        // $validator = new Validator();
        // $validateEmail = $validator->ValidateEmail($Email);        

        // if (!$validateEmail) {
        //     return $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
        // }
        // $validatePassword = $validator->ValidatePassword($password);

        // if (!$validatePassword) {
        //     return $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
        // }
        $checkLogIn = new LoginModel();
        $loginResponse = $checkLogIn->checkLogIn($Email, $this->container);

        if (!$loginResponse) {
            return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], USER_NOT_FOUND);
        }

        if (password_verify($password, $loginResponse['password'])) {
            $token = JWT::encode(['id' => $loginResponse['id'], 'email' => $Email], $this->settings['jwt']['secret'], "HS256");
            return $response->withJSON(['token' => $token], SUCCESS_RESPONSE);
        }
        return $response->withJSON(['error' => true, 'message' => 'Invalid Email or Password.'], INVALID_CREDINTIAL);
    }
}
