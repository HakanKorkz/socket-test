<?php
//echo "<pre>";

//$_SERVER["REQUEST_METHOD"]="OPTIONS";
use Socket\Client\Controllers\TestController;
use Socket\Client\Router\Router;

$router = new Router();
$router->get('/test-static', [TestController::class,"mainStatic"]);
$router->get('/test', function () {
    $Test=new TestController();
   echo $Test->main();

});

$router->prefix('/', function ($router) {
    $router->get('/', function() {
        echo "hoşgeldiniz!";
    });
    $router->any(['/blogs','/blog/{blog}'], function ($blog=null) { // tüm methodları kabul eder
     if (is_null($blog)) {
         echo "Bloglar";
     } else {
         echo "Blog: $blog";
     }
    });



});


$router->prefix('/admin', function($router) {
    $router->get(['/',''], function() { // birden fazla rota girilebilinir
        echo "Admin paneline hoşgeldiniz!";
    });
    $router->get('/users', function() {
        echo "Kullanıcı listesi";
    });
    $router->get('/user/{user}', function($user) {
        echo "Kullanıcı: $user";
    });

    $router->custom("GET","/user/custom/sil/{id}",function ($id) { // özeleştrilebilinir rota
        echo "Kullanıcı idi: $id silindi";
    });
    $router->delete("/user/sil/{id}",function ($id) {
        echo "Kullanıcı idi: $id silindi";
    });

    $router->patch("/user/patch/{id}",function ($id) {
        echo "Kullanıcının ismi  idi: $id değişti";
    });

    $router->put("/user/put/{id}",function ($id) {
        echo "Kullanıcının bilgilileri id: $id değişti";
    });
    $router->options("/options",function () {
        echo "Opsiyonel";
    });
});


$router->dispatch();
