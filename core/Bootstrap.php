<?php


namespace Core;

use Exception;
use ricwein\FileSystem\Exceptions\AccessDeniedException;
use ricwein\FileSystem\File;
use ricwein\FileSystem\Storage\Disk;


class Bootstrap
{
    protected Router $router;

    public function __construct($timezone = 'Asia/Tehran')
    {
        error_reporting(E_ALL);
        set_error_handler([Error::class, 'errorHandler']);
        set_exception_handler([Error::class, 'exceptionHandler']);
        register_shutdown_function([Error::class, 'fatalShutdown']);


        date_default_timezone_set($timezone);

        Session::init();

        (new DotEnv(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env'))->load();


        $this->router = new Router();
    }

    public function run()
    {
        $request = parse_url($_SERVER['QUERY_STRING'], PHP_URL_PATH);

        if (preg_match('/^api/', $_SERVER['QUERY_STRING'])) {
            $request = preg_replace('/^api\//', '', $request);
        }

        try {
            $this->router->dispatch($request);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}