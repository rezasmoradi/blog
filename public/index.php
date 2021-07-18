<?php

use Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$router = new Router();

$router->add('/', ['controller' => 'HomeController', 'action' => 'index']);
$router->get('/login', ['controller' => 'HomeController', 'action' => 'index']);
$router->post('/login', ['controller' => 'HomeController', 'action' => 'index']);
$router->post('/logout', ['controller' => 'HomeController', 'action' => 'index']);

try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch (Exception $e) {
    echo $e->getMessage();
}