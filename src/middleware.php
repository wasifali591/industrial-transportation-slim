<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/private",
    "header"=>"Authorization",
    "attribute" => "decode_token_data",
    "secure" => false,
    "secret" => "truckage",
    "algorithm" => ["HS256"],
    "error" => function($response,$arguments){
        $data["status"]="error";
        $data["message"]=$arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));
