<?php
use Socket\Client\Controllers\TestController;
use Socket\Client\Router\Router;

//echo "<pre>";
$router = new Router();
$router->prefix("/", function ($router) {
    $router->get('/', [TestController::class,"mainStatic"]);
    $router->post('/elephant-io', function () use ($router) {
        TestController::elephantIoStatic($router->postOrJsonControl($_POST));
    });
});


$router->dispatch();
