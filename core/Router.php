<?php

namespace Core;

use Exception;

class Router
{
    protected static array $routes = [];

    protected static array $params = [];

    protected static string $namespace = 'App\\Controllers\\';

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
        foreach (self::getRoutes() as $route => $params) {
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
        if (strpos($url, '.') !== false) {
            try {
                header('Content-Type: ' . mime_content_type(app_disk($url)));
                if (file_exists(app_disk($url))) {
                    readfile(app_disk($url));
                } else {
                    response(['message' => 'file not found'], 404);
                }
            } catch (\Exception $e) {
                Log::error($e);
                response(['message' => 'A error has occurred in the server'], 404);
            }
        } else {
            $url = $this->removeQueryStringVariables($url);

            if ($this->match($url)) {
                if (is_callable(self::$params)) {
                    call_user_func(self::$params);
                } else {
                    self::$params['controller'] = $this->getNamespace() . self::$params['controller'];
                    $controller = self::$params['controller'];

                    if (class_exists($controller)) {
                        $controller_object = new Controller();
                        Request::build(self::$params['params']);

                        $action = self::$params['action'];

                        if ($controller_object->before() == true) {
                            call_user_func_array([$controller_object, $action], $this->getParams());
                            $controller_object->after();
                        }
                    } else {
                        throw new Exception(sprintf('Controller class %s not found', $controller));
                    }
                }
            } else {
                throw new Exception('No route matched!', 404);
            }
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
            return $url;
        }
    }
}