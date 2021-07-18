<?php

namespace Core;

use eftec\bladeone\BladeOne;
use Exception;

class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . '/app/views/' . $view . '.php';

        try {
            if (is_readable($file)) {
                require $file;
            } else {
                throw new Exception('View ' . $file . ' not found');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function renderTemplate($view, $args = [])
    {
        $views = dirname(__DIR__) . '/app/views';
        $cache = dirname(__DIR__) . '/storage/cache';

        $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

        try {
            echo $blade->run($view, $args);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}