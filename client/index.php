<?php

//use Socket\Client\config\CorsHandler;

error_reporting(E_ALL);

ini_set("display_errors", 1);

@ob_start();

@session_start();

date_default_timezone_set('Europe/Istanbul');
require_once __DIR__ . "/vendor/autoload.php";
//$cross = new CorsHandler("allowedOrigins.json");
//$cross->handleCors();
require_once __DIR__ . "/routers.php";
