<?php

namespace Dileep\Mvc\Core;

class Router
{
    protected array $routes = [];

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../../routes/web.php';
    }

    public function handleRequest(): mixed
    {
        // Create Request object
        $request = new Request();

        error_log("=== REQUEST: " . $request->getMethod() . " " . $request->getUrl() . " ===");

        $method = $request->getMethod();
        $url    = $request->getUrl();

        // Method check
        if (!isset($this->routes[$method])) {
            http_response_code(405);
            return [
                'status'  => false,
                'message' => 'Method Not Allowed'
            ];
        }

        // Setup container
        $container       = Container::getInstance();
        $serviceProvider = new AppServiceProvider();
        $serviceProvider->register($container);

        // Setup dispatcher
        $dispatcher = new Dispatcher(
            $container,
        );

        // Match route
        foreach ($this->routes[$method] as $routePath => $action) {
            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $dispatcher->dispatch($action, $params);
            }
        }

        // Route not found
        http_response_code(404);
        return [
            'status'  => false,
            'message' => 'Route not found'
        ];
    }
}