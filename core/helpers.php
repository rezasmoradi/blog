<?php


namespace Core;

if (!function_exists('dd')) {
    function dd(...$args)
    {
        if (is_array($args)) {
            foreach ($args as $arg) {
                echo '<pre>';
                echo '<br/>';
                var_dump($arg);
                echo '<br/>';
            }
        } else {
            echo '<pre>';
            var_dump($args);
        }
        die();
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }
}

if (!function_exists('response')) {
    function response($data, int $responseCode = 200)
    {
        http_response_code($responseCode);
        echo json_encode(['message' => $data]);
    }
}