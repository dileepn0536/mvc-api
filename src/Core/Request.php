<?php

namespace Dileep\Mvc\Core;

class Request
{
    private string $url;
    private string $method;
    private array $body;
    private array $params = [];

    public function __construct()
    {
        $this->url    = $this->parseUrl();
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->body   = $this->parseBody();
    }

    private function parseUrl(): string
    {
        // http://localhost/oops/mvc/public/users
        $url        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // oops/mvc/public/users
        $scriptName = $_SERVER['PHP_SELF']; // oops/mvc/public/index.php
        $base       = dirname($scriptName); //oops/mvc/public

        // base should not be / and also url is part of the base
        if ($base !== '/' && strpos($url, $base) === 0) {
            // remove the portion of the url and return the substring
            $url = substr($url, strlen($base));
        }

        // /users//show remove //
        $url = preg_replace('#/+#', '/', $url);
        // trim both sides if / is there
        $url = trim($url, '/');
        // convert to small case
        return strtolower($url);
    }

    private function parseBody(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return $data ?? [];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * sets url parameters to the router
     * ex: /users/{id} -> [id => 5]
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}