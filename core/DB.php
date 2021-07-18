<?php


namespace Core;


use PDO;

final class DB extends PDO
{
    protected static ?DB $instance = null;

    public static function getInstance(): DB
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8;';
            try {
                self::$instance = new static($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'));
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $exception) {
                echo 'Connection failed! ' . $exception->getMessage();
            }
        }

        return static::$instance;
    }
}