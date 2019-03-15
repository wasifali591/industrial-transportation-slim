<?php
use Slim\Http\Request;
use Slim\Http\Response;


$app->group('/api', function(\Slim\App $app) {
 
 $app->get('/user',function(Request $request, Response $response, array $args) {
    
    
     echo "Wasif";
 });

});