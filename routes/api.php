<?php


use Core\Router;

Router::put('/user/update', ['controller' => 'UserController', 'action' => 'update']);
Router::post('/register', ['controller' => 'UserController', 'action' => 'create']);
