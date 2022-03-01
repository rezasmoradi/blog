<?php


use Core\Router;

/*Router::put('/user/update', ['controller' => 'UserController', 'action' => 'update']);
Router::post('/register', ['controller' => 'UserController', 'action' => 'create']);*/


Router::post('/login', ['controller' => 'UserController', 'action' => 'login']);
Router::get('/user', ['controller' => 'UserController', 'action' => 'index', 'params' => ['auth' => true]]);
Router::post('/register', ['controller' => 'UserController', 'action' => 'register']);
Router::put('/user/update/{id}', ['controller' => 'UserController', 'action' => 'update', 'params' => ['auth' => true]]);
Router::delete('/user/{id}', ['controller' => 'UserController', 'action' => 'delete', 'params' => ['auth' => true]]);
Router::get('/user/follow', ['controller' => 'UserController', 'action' => 'follow', 'params' => ['auth' => true]]);
Router::get('/user/unfollow', ['controller' => 'UserController', 'action' => 'unfollow', 'params' => ['auth' => true]]);

Router::get('/post', ['controller' => 'PostController', 'action' => 'index', 'params' => ['auth' => true]]);
Router::get('/post/show/{id}', ['controller' => 'PostController', 'action' => 'show']);
Router::post('/post', ['controller' => 'PostController', 'action' => 'create', 'params' => ['auth' => true]]);
Router::post('/post/upload', ['controller' => 'PostController', 'action' => 'upload', 'params' => ['auth' => true]]);
Router::put('/post/update/{id}', ['controller' => 'PostController', 'action' => 'update', 'params' => ['auth' => true]]);
Router::delete('/post/{id}', ['controller' => 'PostController', 'action' => 'delete', 'params' => ['auth' => true]]);
Router::get('/post/like/{id}', ['controller' => 'PostController', 'action' => 'like', 'params' => ['auth' => true]]);
Router::get('/post/unlike/{id}', ['controller' => 'PostController', 'action' => 'unlike', 'params' => ['auth' => true]]);
Router::delete('/post/{id}', ['controller' => 'PostController', 'action' => 'delete', 'params' => ['auth' => true]]);