<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

if(file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

require_once __DIR__ . '/../src/Core/helpers.php';

$env = env('APP_ENV') ?: 'prod';


if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

try {
    $router = new Dileep\Mvc\Core\Router();
    $response = $router->handleRequest();

    if(is_array($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo $response;
    }
} catch(Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    
    $errorData = [
        'status' => false,
        'error' => 'An unexpected server error occurred.',
    ];

    if ($env === 'dev') {
        $errorData['details'] = $e->getMessage();
        $errorData['file'] = $e->getFile();
        $errorData['line'] = $e->getLine();
    } else {
        // In production, you might want to log the error details instead of exposing them to the client.
        error_log($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    }
    echo json_encode($errorData);
}