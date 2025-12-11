<?php

namespace App;

spl_autoload_register(function ($class){
    $class = str_ireplace(["\\", "App"], ["/", ".."],$class);
    if(file_exists($class.".php")){
        include $class.".php";
    }
});

$requestUri = strtok($_SERVER["REQUEST_URI"], "?");
$requestUri = urldecode($requestUri);
if(strlen($requestUri)>1)
    $requestUri = rtrim($requestUri, "/");

$routes = yaml_parse_file("../routes.yml");

$dynamicRoutes = [
    "/page/"       => "view",
    "/edit_page/"  => "edit",
    "/delete_page/" => "delete"
];

foreach ($dynamicRoutes as $prefix => $method) {

    if (strpos($requestUri, $prefix) === 0) {

        $slug = substr($requestUri, strlen($prefix));

        $controllerFile = "../Controllers/Page.php";
        if (!file_exists($controllerFile)) {
            die("Aucun fichier controller pour cette uri");
        }
        include $controllerFile;

        $controllerClass = "App\\Controllers\\Page";
        if (!class_exists($controllerClass)) {
            die("La classe du controller n'existe pas");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            die("La methode du controller n'existe pas");
        }

        $controller->$method($slug);
        exit();
    }
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