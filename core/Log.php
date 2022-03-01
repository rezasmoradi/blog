<?php


namespace Core;


class Log
{
    const LOG_DIR = '../storage/logs/';

    public static function exception(\Exception $exception)
    {
        $log = self::LOG_DIR . date('Y-m-d') . '.log';
        ini_set('error_log', $log);

        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

        error_log($message);
    }

    public static function error(\Error $error)
    {
        $log = self::LOG_DIR . date('Y-m-d') . '.log';
        ini_set('error_log', $log);

        $message = "Uncaught exception: '" . get_class($error) . "'";
        $message .= " with message '" . $error->getMessage() . "'";
        $message .= "\nStack trace: " . $error->getTraceAsString();
        $message .= "\nThrown in '" . $error->getFile() . "' on line " . $error->getLine();

        error_log($message);
    }
}