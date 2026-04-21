<?php
class Router
{
    public function handleRequest()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');
        $url = strtolower($url);

        $method = $_SERVER['REQUEST_METHOD'];

        $routes = [
            'GET' => [
                'users' => 'UserController@shouldNotExist',
            ],
            'POST' => [
                'users/store' => 'UserController@storeUser'
            ],
            'PUT' => [
                'users/update' => 'UserController@updateUser'
            ],
            'DELETE' => [
                'users/delete' => 'UserController@deleteUser'
            ]
        ];

        // define routes

        if(isset($routes[$method][$url])) {
            $action = $routes[$method][$url];
            // split the string into controller and method, ex: UserController@index => ['UserController', 'index']
            list($controllerName, $methodName) = explode('@', $action);
            if(!class_exists($controllerName)) {
                $this->sendJsonResponse([
                    'status' => false,
                    'message' => "Controller $controllerName not found"
                ], 500);
            }

            $controller = match ($controllerName) {
                'UserController' => new UserController(new UserService(new UserRepository())),
                default => null
            };

            if($controller && method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                $this->sendJsonResponse([
                    'status' => false,
                    'message' => "Method $methodName not found in controller $controllerName"
                ], 404);
            }
        } else {
            $this->sendJsonResponse([
                'status' => false,
                'message' => "Route not found"
            ], 404);
        }
    }

    private function sendJsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}
