<?php

namespace Core;

use Exception;

class Controller
{
    protected $routeParams = [];

    public function __construct($params)
    {
        $this->routeParams = $params;
    }

    public function __call($name, $args)
    {
        if (method_exists($this, $name)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $name], $args);
                $this->after();
            }
        }else{
            throw new Exception("Method $name not found in controller " . get_class($this));
        }
    }

    public function before()
    {
        return true;
    }

    public function after()
    {
        return true;
    }
}