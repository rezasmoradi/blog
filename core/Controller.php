<?php

namespace Core;

use Exception;

class Controller
{
    protected array $routeParams = [];

    public function __call($name, $args)
    {
        try {
            $controller = new $args['controller']();

            if (method_exists($controller, $name)) {
                if ($this->before() !== false) {
                    $types = ['string', 'array', 'int', 'float', 'bool'];
                    $r = new \ReflectionMethod($args['controller'], $name);
                    $params = $r->getParameters();
                    $p = [];
                    foreach ($params as $param) {
                        if ($param->getType() && !$param->getType()->isBuiltin()) {
                            $par = $param->getType()->getName();
                            if (!in_array($par, $types)) {
                                $p[] = new $par();
                            }
                        }
                    }
                    call_user_func_array([$controller, $name], $p);
                    $this->after();
                }
            }
        } catch (Exception $exception) {
            throw new Exception(sprintf('Method %s not found in controller %s', $name, get_class($this)));
        }
    }

    public function before()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if ($uri === '/api/login' || $uri === '/api/register' || $uri === '/api/forget-password') {
            return true;
        } else {
            if (array_key_exists('auth', $this->routeParams) && $this->routeParams['auth'] === true) {
                return Auth::check(Auth::get_authorization_header());
            }
            return true;
        }
    }

    public function after()
    {
        return true;
    }
}