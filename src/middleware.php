<?php
/**
 * Authentication checking
 * for private api need token to access the api and public api can access by anyone ,
 * authentacation managed by jwt auth
 * Created date : 19/03/2019
 *
 * PHP version 7
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
// Application middleware
$app->add(
    new \Tuupola\Middleware\JwtAuthentication(
        [
          "path" => "/private",
          "header"=>"Authorization",
          "attribute" => "decode_token_data",
          "secure" => false,
          "secret" => "truckage",
          "algorithm" => ["HS256"],
          "error" => function ($response, $arguments) {
            $data["status"]="error";
            $data["message"]=$arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
          }
        ]
    )
);
