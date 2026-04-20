<?php
error_reporting(E_ALL);
$env = 'dev'; // change to 'prod' later

if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

header("Content-Type: application/json");
spl_autoload_register(function ($class) {
    $folders = ['controllers','services','models','core','repositories','config'];
    foreach($folders as $folder) {
        $path = "$folder/$class.php";
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

try {
    $router = new Router();
    $router->handleRequest();
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Unexpected error'
    ]);
}