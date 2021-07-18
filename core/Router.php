<?php

namespace Core;

use Exception;

class Router
{
    protected $routes = [];

    protected $params = [];

    protected $namespace = 'App\Controllers\\';

    public function add($route, $params = [])
    {
        $route = preg_replace('/^\//', '', $route);
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/{([a-z]+)}/', '(?P<\1>[a-z0-9-]+)', $route);

        $route = '/^' . $route . '\/?$/i';

        $this->routes[$route] = $params;
    }

    public function __call($name, $args)
    {
        if (gettype($args[1]) === 'object') {
            self::add($args[0], $args[1]);
        } else {
            $args[1]['method'] = strtoupper($name);
            self::add($args[0], $args[1]);
        }
    }

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                if ($method = $this->checkMethod(array_keys($params))) {
                    $this->setParams($matches, $params[$method]);
                    return true;
                }
            }
        }
        return false;
    }

    private function checkMethod($methods)
    {
        if (count($methods) > 1) {
            foreach ($methods as $method) {
                if ($method === $_SERVER['REQUEST_METHOD']) {
                    return $method;
                }
            }
        } else {
            return $methods[0] === $_SERVER['REQUEST_METHOD'] ? $methods[0] : false;
        }
        return false;
    }

    private function setParams($matches, $params)
    {
        foreach ($matches as $key => $match) {
            if (is_string($key)) {
                $params['params'][$key] = $match;
            }
        }
        $this->params = $params;
    }

    public function dispatch($url)
    {
        $url = $this->removeVariablesOfQueryString($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $obj = new $controller($this->params);

                $action = $this->params['action'];
                $obj->$action();
            } else {
                throw new Exception("Controller class $controller not found!");
            }
        } else {
            throw new Exception('Route not matched!');
        }
    }

    public function removeVariablesOfQueryString($url)
    {
        if ($url !== '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    public function getNamespace()
    {
        if (array_key_exists('namespace', $this->params)) {
            $this->namespace .= $this->params['namespace'];
        }

        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}