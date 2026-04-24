<?php
$env = getenv('APP_ENV') ?: 'env';

if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

spl_autoload_register(function ($class) {
    // This is your "Address Book"
    // It maps the Class Name to the exact File Path
    $classMap = [
        // Core
        'Router'         => 'core/Router.php',
        'Database'       => 'core/Database.php',
        'Cache'          => 'core/Cache.php',
        
        // Controllers
        'UserController' => 'controllers/UserController.php',
        'TestController' => 'controllers/TestController.php',
        
        // Services
        'UserService'    => 'services/UserService.php',
        'NotificationInterface' => 'services/NotificationInterface.php',
        'SMSNotification' => 'services/SMSNotification.php',
        'EmailNotification' => 'services/EmailNotification.php',
        'NotificationFactory' => 'services/NotificationFactory.php',
        
        // Repositories
        'UserRepository' => 'repositories/UserRepository.php',
        
        // Models
        'User'           => 'models/User.php',
    ];

    if (isset($classMap[$class])) {
        $path = $classMap[$class];
        if (file_exists($path)) {
            require_once $path;
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
        'message' => 'Unexpected error',
        "details" => $e->getMessage()
    ]);
}