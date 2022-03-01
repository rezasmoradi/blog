<?php


namespace Core;


use App\Config;

class Error
{
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0 && getenv('SHOW_ERROR')) {
            var_dump('error: ' .  $message,'file: '. $file, 'line: '. $line);
        }
    }

    /**
     * @param \Exception $exception
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();
        if ($code !== 404) {
            $code = 500;
        }
        http_response_code($code);

        if (getenv('SHOW_ERROR')) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";

//            View::renderTemplate("errors/$code");

        } else {
            $log = dirname(__DIR__) . '/storage/logs/' . date('Y-m-d') . '.log';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);
        }
    }

    public static function fatalShutdown()
    {
        return error_get_last();
    }
}