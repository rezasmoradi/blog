<?php


namespace Core;

class Session
{
    private static $sessionId;

    public static function init()
    {
        if (empty(session_id())) {
            session_start();
            self::$sessionId = session_id();
        }
    }

    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function get($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public static function forget($name)
    {
        unset($_SESSION[$name]);
    }

    public static function pull($name)
    {
        $value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
        if (!is_null($value)) {
            unset($_SESSION[$name]);
        }
        return $value;
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function clear()
    {
        session_destroy();
    }
}