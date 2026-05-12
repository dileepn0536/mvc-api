<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dileep\Mvc\Core\ExceptionHandler;
use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Kolkata');

require_once __DIR__ . '/../src/Core/helpers.php';

$env = $_ENV['APP_ENV'] ?? 'prod';


if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

header('Content-Type: application/json; charset=utf-8');

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

if($env == "dev") {
    $flags |= JSON_PRETTY_PRINT;
}
try {
    $router = new Dileep\Mvc\Core\Router();
    $response = $router->handleRequest();

    echo is_array($response) ? json_encode($response, $flags) : $response;
    exit;
} catch(Throwable $e) {

    $response = ExceptionHandler::handle($e);
    http_response_code($response['code']);

    if ($env === 'dev') {
        $response['details'] = $e->getMessage();
        $response['file'] = $e->getFile();
        $response['line'] = $e->getLine();
    } else {
        // In production, you might want to log the error details instead of exposing them to the client.
        error_log($e->getMessage() . 
                  " in " . $e->getFile() . 
                  " on line " . $e->getLine());
    }
    echo json_encode($response, JSON_THROW_ON_ERROR);
    exit;
}