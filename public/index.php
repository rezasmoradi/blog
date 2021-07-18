<?php

require '../core/Router.php';

spl_autoload_register(function ($className) {
    require $className . '.php';
});

$router = new Router();

$router->add('/', ['controller' => 'HomeController', 'action' => 'index']);

try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch (Exception $e) {
    echo $e->getMessage();
}