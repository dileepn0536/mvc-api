<?php

namespace Dileep\Mvc\Core;

use Exception;

class Router
{
    protected array $routes = [];

    public function __construct()
    {
        // Load routes from external file (clean design)
        $this->routes = require __DIR__ . '/../../routes/web.php';
    }

    public function handleRequest()
    {

        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Normalize base path
        $scriptName = $_SERVER['PHP_SELF'];
        $base = dirname($scriptName);

        if ($base !== '/' && strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
        }

        // Normalize URL
        $url = preg_replace('#/+#', '/', $url); // remove duplicate slashes
        $url = trim($url, '/');
        $url = strtolower($url);

        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // 🚫 Method check
        if (!isset($this->routes[$method])) {
            http_response_code(405);
            return json_encode([
                'status' => false,
                'message' => 'Method Not Allowed'
            ]);
        }

        // ✅ Initialize container once
        $container = Container::getInstance();

        // Bind DB (example)
        $container->bind(\PDO::class, function () {
            return Database::getInstance()->getConnection();
        });

        foreach ($this->routes[$method] as $routePath => $action) {

            // Convert {param} → regex
            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $url, $matches)) {

                // ✅ Extract named parameters
                // $matches contains both numeric and named keys, we only want named ones
                // Using array_filter with ARRAY_FILTER_USE_KEY to keep only named keys
                // This way we avoid issues with numeric keys and ensure we only get the parameters defined in the route
                // what we want is something like ['id' => '123'] if the route is users/show/{id} and the URL is users/show/123
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if(!str_contains($action, '@')) {
                    throw new Exception("Invalid route action format");
                }

                [$controllerName, $methodName] = explode('@', $action);
                $fullControllerClass = 'Dileep\\Mvc\\Controllers\\' . $controllerName;

                try {
                    // ✅ Safety checks
                    if (!class_exists($fullControllerClass)) {
                        throw new Exception("Controller not found");
                    }

                    $controller = $container->resolve($fullControllerClass);

                    if (!method_exists($controller, $methodName)) {
                        throw new Exception("Method not found");
                    }


                    $params = array_values($params); // reindex for call_user_func_array
                    $response = call_user_func_array([$controller, $methodName], $params);
                    return $response;

                } catch (\Throwable $e) {
                    http_response_code(500);

                    // ❌ Don't expose internal errors
                    return [
                        'status' => false,
                        'message' => 'An unexpected error occurred.'
                    ];
                }
            }
        }

        // ❌ Route not found
        http_response_code(404);
        return [
            'status' => false,
            'message' => 'Route not found'
        ];
    }
}