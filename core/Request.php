<?php


namespace Core;


class Request
{
    protected $params = [];

    public function __construct($routeParams)
    {
        if ($routeParams) {
            $this->params = $routeParams['params'];
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        $requests = [];
        $url = explode('&', $_SERVER['QUERY_STRING']);
        unset($_GET[$url[0]]);
        if (!empty($_GET)) {
            $requests[] = $_GET;
        }
        if (!empty($_POST)) {
            $requests[] = $_POST;
        }
        if (!empty($_FILES)) {
            $requests[] = $_FILES;
        }
        return $requests;
    }

    /**
     * @param array|string $requests
     * @return string|array|null
     */
    public function get($requests = ['*'])
    {
        return $this->checkRequests($requests, $_GET);
    }

    /**
     * @param array|string $requests
     * @return array|string|null
     */
    public function post($requests = ['*'])
    {

        return $this->checkRequests($requests, $_POST);
    }

    /**
     * @param array|string $requests
     * @return array|string|null
     */
    public function files($requests = ['*'])
    {
        return $this->checkRequests($requests, $_FILES);
    }

    private function checkRequests($requests, $type = null)
    {
        switch (true) {
            case is_array($requests) && $requests[0] === '*':
                return $type;
            case is_array($requests):
                $getRequests = [];
                foreach ($requests as $request) {
                    if ($type[$request]) {
                        $getRequests[] = $type[$request];
                    }
                }
                return $getRequests;
            case is_string($requests):
                if (array_key_exists($requests, $type)) {
                    return $type[$requests] ?? null;
                } else {
                    return $this->params[$requests];
                }
            default:
                return null;
        }
    }
}