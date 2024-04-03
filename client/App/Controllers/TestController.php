<?php

namespace Socket\Client\Controllers;

use ElephantIO\Client;
use Socket\Client\Router\View;

class TestController
{

    public static function mainStatic(): null
    {
        return View::render("index");
    }

    public static function elephantIoStatic($request)
    {

        $elephantUrl = "ws://localhost:3000";

        $options = ["client" => Client::CLIENT_4X];

        $client = Client::create($elephantUrl, $options);

        $client->emit("php-client", ["data" => "abcd"]);

        echo "<pre>";
        var_dump($client);


    }


}