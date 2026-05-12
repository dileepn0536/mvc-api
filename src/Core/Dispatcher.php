<?php

namespace Dileep\Mvc\Core;

use Exception;

class Dispatcher
{
    public function __construct(
        private Container $container
    ) {}

    public function dispatch(string $action, array $params): mixed
    {
        if (!str_contains($action, '@')) {
            throw new Exception("Invalid route action format");
        }

        [$controllerName, $methodName] = explode('@', $action);
        $fullControllerClass = 'Dileep\\Mvc\\Controllers\\' . $controllerName;

        try {
            if (!class_exists($fullControllerClass)) {
                throw new Exception("Controller not found");
            }

            $controller = $this->container->resolve($fullControllerClass);

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method not found");
            }

            $params = array_values($params);
            return call_user_func_array([$controller, $methodName], $params);

        } catch (\Throwable $e) {

            $response = ExceptionHandler::handle($e);
            http_response_code($response['code']);
            return $response;
        }
    }
}