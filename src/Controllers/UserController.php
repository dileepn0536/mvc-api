<?php

namespace Dileep\Mvc\Controllers;

use Dileep\Mvc\Services\UserService;
use Dileep\Mvc\Core\Cache;
use Exception;

class UserController
{
    private $userService;
    private $cache;

    public function __construct(UserService $userService , Cache $cache)
    {
        $this->userService = $userService;
        $this->cache = $cache;
    }

    public function index()
    {
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = ($page - 1) * $limit;

        // controller doesn't know about cache at all!
        $users = $this->userService->getUsers($limit, $offset);

        return [
            'status' => true,
            'data' => $users
        ];
    }

    public function cacheCheck()
    {
        $files = glob('cache/*.json');
        return [
            'status' => true,
            'cache_files' => $files,
            'count' => count($files)
        ];
    }

    public function createUser()
    {
        ob_start();
        require_once "views/createuser.php";
        return ob_get_clean();
    }

    public function storeUser()
    {
        $data = $this->getJsonData();
     
        if ($data === null) {
            http_response_code(400);
            return [
                'status' => false,
                'message' => 'Invalid JSON input'
            ];
        }
        
        $name = $data['name'] ?? "";
        $email = $data['email'] ?? "";

        if(empty($name) || empty($email)) {
            return [
                'status' => false,
                'message' => "Name and Email are required"
            ];
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => "Email is invalid"
            ];
        }

        try {
            $this->userService->createUser($name, $email);
            http_response_code(201);
            return [
                'status' => true,
                'message' => "User created successfully"
            ];
        } catch(Exception $e) {
            http_response_code(500);
            return [
                'status' => false,
                'message' => "Internal server error"
            ];
        }
    }

    public function getEditUser()
    {
        $id = $_GET['id'] ?? "";

        if(!$id) {
            http_response_code(400);
            return "User not found";
        }

        $userinfo = $this->userService->getUserById($id);

        if(!$userinfo) {
            http_response_code(404);
            return "User not found";
        }
        
        ob_start();
        require_once "views/getedituser.php";
        return ob_get_clean();
    }

    public function updateUser($id)
    {
        $data = $this->getJsonData();
        
        if ($data === null) {
            http_response_code(400);
            return [
                'status' => false,
                'message' => 'Invalid JSON input'
            ];
        }

        $name = $data['name'] ?? "";
        $email = $data['email'] ?? "";

        if (empty($name) || empty($email)) {
            http_response_code(400);
            return [
                'status' => false,
                'message' => "All fields are required"
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return [
                'status' => false,
                'message' => "Invalid email format"
            ];
        }

        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            http_response_code(404);
            return [
                'status' => false,
                'message' => "User not found"
            ];
        }

        try {
            $this->userService->updateUser($id, $name, $email);
            http_response_code(200);
            return [
                'status' => true,
                'message' => "User updated successfully"
            ];
        } catch(Exception $e) {
            http_response_code(500);
            return [
                'status' => false,
                'message' => "Internal server error"
            ];
        }
    }

    public function deleteUser($id)
    {
        $data = $this->getJsonData();
        if ($data === null) {
            http_response_code(400);
            return [
                'status' => false,
                'message' => 'Invalid JSON input'
            ];
        }
        
        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            http_response_code(404);
            return [
                'status' => false,
                'message' => "User not found"
            ];
        }

        try {
            $this->userService->deleteUser($id);
            http_response_code(200);
            return [
                'status' => true,
                'message' => "User deleted successfully"
            ];
        } catch(Exception $e) {
            http_response_code(500);
            return [
                'status' => false,
                'message' => "Internal server error"
            ];
        }
    }

    public function showUser($id)
    {
        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            http_response_code(404);
            return [
                'status' => false,
                'message' => "User not found"
            ];
        }

        return [
            'status' => true,
            'data' => $userinfo
        ];
    }

    private function getJsonData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }
}