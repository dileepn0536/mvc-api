<?php

namespace Dileep\Mvc\Core;

// Notice we don't even need to 'use' the specific Controllers or Services anymore!
// The Container will find them automatically.
use Exception;

class Router
{
    public function handleRequest()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');
        $url = strtolower($url);

        $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if($scriptDir && strpos($url, $scriptDir) === 0) {
            $url = ltrim(substr($url, strlen($scriptDir)), '/');
        }

        $method = $_SERVER['REQUEST_METHOD'];

        $routes = [
            'GET' => [
                'users' => 'UserController@index',
                'users/test' => 'UserController@test',
                'users/debugging' => 'TestController@testDebugging',
                'cache-check' => 'UserController@cacheCheck',
                'users/create' => 'UserController@createUser',
                'users/show' => 'UserController@showUser'
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

        // 1. Instantiate your new Container!
        $container = new \Dileep\Mvc\Core\Container();

        // 2. OPTIONAL: You can bind specific implementations if you want, or just let it auto-wire everything!
        // For example, if you wanted to bind an interface to a specific class, you could do it here:
        $container->bind(\PDO::class, function() {
            return \Dileep\Mvc\Core\Database::getInstance()->getConnection();
        });

        // define routes
        if(isset($routes[$method][$url])) {
            $action = $routes[$method][$url];
            list($controllerName, $methodName) = explode('@', $action);
            
            $fullControllerClass = 'Dileep\Mvc\Controllers\\' . $controllerName;

            if(!class_exists($fullControllerClass)) {
                http_response_code(500);
                // We just return the array now! No more echo or exit.
                return [
                    'status' => false,
                    'message' => "Controller $controllerName not found"
                ];
            }

            // 2. THE MAGIC: Let the container build the controller and all its dependencies!
            try {
                $controller = $container->resolve($fullControllerClass);
            } catch (Exception $e) {
                // If the container fails to build a dependency, catch it here
                http_response_code(500);
                return [
                    'status' => false,
                    'message' => "Dependency Injection Error: " . $e->getMessage()
                ];
            }

            if($controller && method_exists($controller, $methodName)) {
                // 3. Execute the method and return the data back to index.php
                return $controller->$methodName();
            } else {
                http_response_code(404);
                return [
                    'status' => false,
                    'message' => "Method $methodName not found in controller $controllerName"
                ];
            }
        } else {
            http_response_code(404);
            return [
                'status' => false,
                'message' => "Route not found"
            ];
        }
    }
}