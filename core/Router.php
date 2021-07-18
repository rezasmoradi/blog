<?php

require '../app/controllers/HomeController.php';

class Router
{
    protected $routes = [];

    protected $params = [];

    public function add($route, $params = [])
    {
        $route = preg_replace('/^\//', '', $route);
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/{([a-z]+)}/', '(?P<\1>[a-z0-9-]+)', $route);

        $route = '/^' . $route . '\/?$/i';

        $this->routes[$route] = $params;
    }

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatch($url)
    {
        $url = $this->removeVariablesOfQueryString($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];

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