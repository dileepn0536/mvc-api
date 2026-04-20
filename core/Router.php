<?php
class Router
{
    public function handleRequest()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');
        $url = strtolower($url);
        // $url = str_replace('oops/mvc', '', $url);
        echo $url;die;

        $method = $_SERVER['REQUEST_METHOD'];

        $userRepository = new UserRepository();
        $userService = new UserService($userRepository);
        $controller = new UserController($userService);

        if ($url === '/users' && $method === 'GET') {
            $controller->index();

        } elseif ($url === '/users/store' && $method === 'POST') {
            $controller->storeUser();

        } elseif ($url === '/users/update' && $method === 'PUT') {
            $controller->updateUser();

        } elseif ($url === '/users/delete' && $method === 'DELETE') {
            $controller->deleteUser();

        } else {
            http_response_code(404);
            echo json_encode([
                'status' => false,
                'message' => "Route not found"
            ]);
        }
    }
}
