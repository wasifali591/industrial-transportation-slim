<?php
    namespace App\api\controllers;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Http\Response as Response;
    use \Firebase\JWT\JWT;
    use Interop\Container\ContainerInterface;
    use App\api\services\LoginService;
    
    require __DIR__ . '/../../validation/validator.php';
    require_once __DIR__ . '/../../constants/EndPoints.php';

    class LoginController{
        public $container;
        public $settings;
        public function __construct(ContainerInterface $container){
            $this->container = $container->get('db');
            $this->settings=$container->get('settings');
        }
        public function home($request, $response)    {
            $Email = $request->getParsedBody()['username'];
            $password = $request->getParsedBody()['password'];

            if ($Email == '' || $password == '') {
                return $response->withJSON(['error' => true, 'message' => 'Email or Password is empty.'], UNAUTHORIZED_USER);
            } else {
                $validateEmail = ValidateEmail($Email);
                if ($validateEmail == false) {
                    $response->withJSON(['error' => true, 'message' => 'Enter valid Email.'], UNAUTHORIZED_USER);
                }

                $validatePassword = ValidatePassword($password);
                if ($validatePassword == false) {
                    $response->withJSON(['error' => true, 'message' => 'Enter valid Password.'], UNAUTHORIZED_USER);
                }
                $checkLogIn = new LoginService( );
                $loginResponse = $checkLogIn->checkLogIn($Email,$this->container);
                if ($loginResponse == false) {
                    return $response->withJSON(['error' => true, 'message' => 'User not found. Please register first.'], USER_NOT_FOUND);
                } else {
                    if (password_verify($password, $loginResponse['password'])) {
                        $token = JWT::encode(['id' => $loginResponse['id'], 'email' => $Email], $this->settings['jwt']['secret'], "HS256");
                        return $response->withJSON(['token' => $token], SUCCESS_RESPONSE);
                    } else {
                        return $response->withJSON(['error' => true, 'message' => 'Invalid Email or Password.'], INVALID_CREDINTIAL);
                    }
                }
            }
        }
    }
