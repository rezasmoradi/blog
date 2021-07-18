<?php


class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . '/app/views/' . $view . '.php';

        if (is_readable($file)) {
            require $file;
        } else {
            throw new Exception('View ' . $file . ' not found');
        }
    }
}