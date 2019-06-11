<?php
/**
 * Depedencies
 * 
 * Database  connection and the classes of controller are return
 * from here which can easyli access  from routes
 * Created date : 19/03/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
    // DIC configuration

    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // Filemaker configuration
    $container['db'] = function ($c) {
        $settings = $c->get('settings')['db'];

        include __DIR__ .'/library/FileMakerCWP/FileMaker.php';

        $fm = new FileMaker();
        $fm->setProperty('database', $settings['database']);
        $fm->setProperty('hostspec', $settings['host']);
        $fm->setProperty('username', $settings['username']);
        $fm->setProperty('password', $settings['password']);
        return $fm;
    };

    //return the class defined path 
    $container['LoginController'] = function ($c) {
        return new App\api\controllers\LoginController($c);
    };
    $container['RegisterController']=function ($c) {
        return new App\api\controllers\RegisterController($c);
    };
    $container['PasswordController']=function ($c) {
        return new App\api\controllers\PasswordController($c);
    };
    $container['UserProfileController']=function ($c) {
        return new App\api\controllers\UserProfileController($c);
    };
    $container['DocumentsController']=function ($c) {
        return new App\api\controllers\DocumentsController($c);
    };    
    $container['TruckController']=function ($c) {
        return new App\api\controllers\TruckController($c);
    };
