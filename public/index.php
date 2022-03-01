<?php

use Core\Bootstrap;

require dirname(__DIR__) . '/vendor/autoload.php';
//require dirname(__DIR__) . '/routes/web.php';
require dirname(__DIR__) . '/routes/api.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;charset=UTF-8');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$app = new Bootstrap();

$app->run();