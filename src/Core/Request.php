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
        $url        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = $_SERVER['PHP_SELF'];
        $base       = dirname($scriptName);

        if ($base !== '/' && strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
        }

        $url = preg_replace('#/+#', '/', $url);
        $url = trim($url, '/');
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

    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}