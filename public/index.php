<?php

use Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('\Core\Error::exceptionHandler');
register_shutdown_function('\Core\Error::fatalShutdown');

$router = new Router();

$router->add('/', ['controller' => 'HomeController', 'action' => 'index', 'method' => 'get']);
$router->get('/login', ['controller' => 'HomeController', 'action' => 'index']);
$router->post('/login', ['controller' => 'HomeController', 'action' => 'index']);
$router->post('/logout', ['controller' => 'HomeController', 'action' => 'index']);

try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch (Exception $e) {
    echo $e->getMessage();
}