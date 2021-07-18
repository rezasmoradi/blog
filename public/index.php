<?php

use Core\Bootstrap;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/core/helpers.php';
require dirname(__DIR__) . '/routes/web.php';

$app = new Bootstrap();

$app->run();