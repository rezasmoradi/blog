<?php


use App\Models\Post;
use Core\Log;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use ricwein\FileSystem\Directory;
use ricwein\FileSystem\Exceptions\Exception;
use ricwein\FileSystem\Storage\Disk;

if (!function_exists('dd')) {
    function dd($args = null)
    {
        echo '<div style="background-color: #1e2125">';
        if (!is_null($args) && (is_array($args) || is_object($args))) {
            echo '<ul>';
            foreach ($args as $key => $arg) {
                dd($key . ' => ' . $arg);
            }
            echo '</ul>';
            die();
        } else {
            echo '<li style="list-style: none;color: #ced4da;padding: 2px;font-family: Consolas, sans-serif;font-size: 12px">' . $args . '</li>';
        }
        echo '</div>';
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

        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo json_encode(['message' => $data]);
        }
    }
}

if (!function_exists('parameterize')) {
    /**
     * @param array $props
     * @param array $fillables
     * @param string $nodeName
     * @return string
     */
    function parameterize($props, $fillables, $nodeName = 'n')
    {
        $result = [];
        foreach ($props as $prop) {
            if (in_array($prop, $fillables)) {
                $result[] = $nodeName . '.' . $prop;
            }
        }
        return implode(', ', $result);
    }
}

if (!function_exists('array_flatten')) {
    function array_flatten($array)
    {
        $return = [];
        foreach ($array as $key => $item) {
            if (is_array($item) && array_key_exists($key, $item)) {
                $return[] = $item[$key];
            } else {
                if (!is_numeric($key) || is_object($item)) {
                    $return[] = $item[$key];
                } else {
                    continue;
                }
            }
        }
        return $return;
    }
}

if (!file_exists('storage_disk')) {
    function storage_disk($path)
    {
        return dirname(__DIR__) . '/storage/' . $path;
    }
}

if (!file_exists('app_disk')) {
    function app_disk($path)
    {
        return dirname(__DIR__) . '/' . $path;
    }
}