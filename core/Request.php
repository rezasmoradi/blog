<?php

namespace Core;

use App\Models\User;

class Request
{
    public bool $isApi;
    protected static array $params = [];
    private ?\Nyholm\Psr7\Request $request = null;

    public function __construct()
    {
        $this->request = new \Nyholm\Psr7\Request($_SERVER['REQUEST_METHOD'], getenv('APP_URL') . $_SERVER['REQUEST_URI'], headers_list());

        $this->isApi = preg_match('/^api/', $_SERVER['QUERY_STRING']);
    }

    public static function build($params = [])
    {
        self::$params = $params;
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function acceptEncoding()
    {
        return $_SERVER['HTTP_ACCEPT_ENCODING'];
    }

    public function headers()
    {
        return get_headers(getenv('APP_URL'), true);
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
        $requests[] = $this->params;
        return $requests;
    }

    /**
     * @param array|string $params
     * @return string|array|null
     */
    public function get($params = '*')
    {
        if ($params === '*') {
            return self::$params;
        } else {
            return array_key_exists($params, self::$params) ? self::$params[$params] : null;
        }
    }

    /**
     * @return array
     */
    public function query()
    {
        $results = [];
        $query = $this->request->getUri()->getQuery();
        $args = explode('&', $query);
        if ($args[0] !== '') {
            foreach ($args as $arg) {
                $param = explode('=', $arg);
                $results[$param[0]] = $param[1];
            }
        }
        return $results;
    }

    /**
     * @param array|string $requests
     * @return array|string|null
     */
    public function post($requests = ['*'])
    {
        if ($this->isApi) {
            $apiRequests = json_decode(file_get_contents('php://input'), true);
            return $apiRequests ? $this->checkRequests($requests, $apiRequests) : null;
        } else {
            return $this->checkRequests($requests, $_POST);
        }
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

    public function user()
    {
        if ($user = json_decode(Session::get('user'))) {
            return User::builder()->find($user->id);
        }
        return null;
    }
}