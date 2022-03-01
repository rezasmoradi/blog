<?php


namespace Core;

use Bolt\Bolt;
use Bolt\connection\Socket;
use Bolt\protocol\AProtocol;

final class DB
{
    protected static ?AProtocol $instance = null;

    public static function getInstance(): AProtocol
    {
        if (self::$instance === null) {
            try {
                $bolt = new Bolt(new Socket('127.0.0.1',11003));
                $protocol = $bolt->build();
                $protocol->hello(\Bolt\helpers\Auth::basic('neo4j', 'rs'));

                static::$instance = $protocol;
            } catch (\Exception $e) {
                echo 'Connection failed! ' . $e->getMessage();
            }
        }

        return static::$instance;
    }

    public static function beginTransaction()
    {
        self::getInstance()->begin();
    }

    public static function commit()
    {
        self::getInstance()->commit();
    }

    public static function rollback()
    {
        self::getInstance()->rollback();
    }
}