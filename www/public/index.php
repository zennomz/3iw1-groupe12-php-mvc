<?php

namespace App;

spl_autoload_register(function ($class){
    $class = str_ireplace(["\\", "App"], ["/", ".."],$class);
    if(file_exists($class.".php")){
        include $class.".php";
    }
});

$requestUri = strtok($_SERVER["REQUEST_URI"], "?");
if(strlen($requestUri)>1)
    $requestUri = rtrim($requestUri, "/");
$requestUri = strtolower($requestUri);

$routes = yaml_parse_file("../routes.yml");

if(strpos($requestUri, "/page/") === 0){
    $slug = substr($requestUri, strlen("/page/"));
    
    if (!file_exists("../Controllers/Page.php")) {
        die("Aucun fichier controller pour cette uri");
    }
    include "../Controllers/Page.php";

    $controller = "App\\Controllers\\Page";
    if (!class_exists($controller)) {
        die("La classe du controller n'existe pas");
    }

    $objetController = new $controller();

    if (!method_exists($objetController, "view")) {
        die("La methode du controller n'existe pas");
    }

    $objetController->view($slug);
    exit();
}

if(empty($routes[$requestUri])){
    die("Aucune route pour cette uri : page 404");
}

if(empty($routes[$requestUri]["controller"]) || empty($routes[$requestUri]["action"]) ){
    die("Aucun controller ou action pour cette uri : page 404");
}

$controller = $routes[$requestUri]["controller"];
$action = $routes[$requestUri]["action"];

if(!file_exists("../Controllers/".$controller.".php")){
    die("Aucun fichier controller pour cette uri");
}

include "../Controllers/".$controller.".php";

$controller = "App\\Controllers\\".$controller;
if(!class_exists($controller)){
    die("La classe du controller n'existe pas");
}

$objetController = new $controller();

if(!method_exists($objetController, $action)){
    die("La methode du controller n'existe pas");
}

$objetController->$action();