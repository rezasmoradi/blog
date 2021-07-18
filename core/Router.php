<?php

namespace Core;

use Exception;

class Router
{
    protected static $routes = [];

    protected static $params = [];

    protected static $namespace = 'App\\Controllers\\';

    public static function add($route, $params = [])
    {
        $route = preg_replace('/^\//', '', $route);
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/{([a-z]+)}/', '(?P<\1>[a-z0-9-]+)', $route);

        $route = '/^' . $route . '\/?$/i';

        $method = 'GET';

        if (gettype($params) === 'array') {
            $method = array_key_exists('method', $params) ? strtoupper($params['method']) : 'GET';
            unset($params['method']);
        }

        self::$routes[$route][$method] = $params;
    }

    public static function __callStatic($name, $args)
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
        foreach (self::$routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                if ($method = $this->checkMethod(array_keys($params))) {
                    $this->setParams($matches, $params[$method]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $methods
     * @return bool
     */
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

    /**
     * @param array $matches
     * @param array|\Closure $params
     *
     * @return void
     */
    private function setParams($matches, $params)
    {
        foreach ($matches as $key => $match) {
            if (is_string($key)) {
                $params['params'][$key] = $match;
            }
        }
        self::$params = $params;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return self::$routes;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return self::$params;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {

        if (array_key_exists('namespace', self::$params)) {
            self::$namespace .= self::$params['namespace'] . '\\';
        }

        return self::$namespace;
    }

    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            if (is_callable(self::$params)) {
                call_user_func(self::$params);
            } else {
                $controller = self::$params['controller'];
                $controller = $this->getNamespace() . $controller;

                if (class_exists($controller)) {
                    $controller_object = new $controller(self::$params);

                    $action = self::$params['action'];

                    unset(self::$params['controller'], self::$params['action']);

                        $controller_object->$action(new Request($this->getParams()));
                } else {
                    throw new Exception("Controller class $controller not found");
                }
            }
        } else {
            throw new Exception('No route matched!', 404);
        }

    }

    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }
}