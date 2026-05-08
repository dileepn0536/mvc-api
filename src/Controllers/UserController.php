<?php

namespace Dileep\Mvc\Controllers;

use Exception;
use Dileep\Mvc\Validators\UserValidator;
use Dileep\Mvc\Controllers\BaseController;
use Dileep\Mvc\Interfaces\UserServiceInterface;
class UserController extends BaseController
{
    private UserServiceInterface $userService;
    private UserValidator $userValidator;

    public function __construct(UserServiceInterface $userService, UserValidator $userValidator)
    {
        $this->userService = $userService;
        $this->userValidator = $userValidator;
    }

    public function index()
    {
        $page  = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
        $limit = min(100, isset($_GET['limit']) ? (int)$_GET['limit'] : 20);
        $offset = ($page - 1) * $limit;

        // controller doesn't know about cache at all!
        $users = $this->userService->getUsers($limit, $offset);

        return $this->jsonResponse(true, $users);
    }

    public function cacheCheck()
    {
        $files = glob('cache/*.json');
        return [
            'status' => true,
            'cache_files' => array_map('basename', $files)
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
            return $this->jsonResponse(false, 'Invalid JSON input', 400);
        }
        
        if (!$this->userValidator->validate($data)) {
            return $this->jsonResponse(false, $this->userValidator->getFirstError(), 400);
        }
        
        $name = trim($data['name']);
        $email = trim($data['email']);

        try {
            $this->userService->createUser($name, $email);
            return $this->jsonResponse(true, "User created successfully", 201);
        } catch(Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return $this->jsonResponse(false, "Internal server error", 500);
        }
    }

    public function getEditUser()
    {
        $id = $_GET['id'] ?? "";

        if(!$id) {
            return $this->jsonResponse(false, "User ID is required", 400);
        }

        $userinfo = $this->userService->getUserById($id);

        if(!$userinfo) {
            return $this->jsonResponse(false, "User not found", 404);
        }
        
        ob_start();
        require_once "views/getedituser.php";
        return ob_get_clean();
    }

    public function updateUser(int $id)
    {
        $data = $this->getJsonData();
        
        if ($data === null) {
            return $this->jsonResponse(false, 'Invalid JSON input', 400);
        }

        if (!$this->userValidator->validate($data)) {
            return $this->jsonResponse(false, $this->userValidator->getFirstError(), 400);
        }

        $name = trim($data['name']);
        $email = trim($data['email']);

        $userinfo = $this->userService->getUserById($id);
    
        if (!$userinfo) {
            return $this->jsonResponse(false, "User not found", 404);
        }

        try {
            $this->userService->updateUser($id, $name, $email);
            return $this->jsonResponse(true, "User updated successfully");
        } catch(Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            return $this->jsonResponse(false, "Internal server error", 500);
        }
    }

    public function deleteUser(int $id)
    {
        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            return $this->jsonResponse(false, "User not found", 404);
        }

        try {
            $this->userService->deleteUser($id);
            return $this->jsonResponse(true, "User deleted successfully");
        } catch(Exception $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return $this->jsonResponse(false, "Internal server error", 500);
        }
    }

    public function showUser(int $id)
    {
        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            return $this->jsonResponse(false, "User not found", 404);
        }

        return $this->jsonResponse(true, $userinfo);
    }

    public function testLock(int $id)
    {
        if(getenv('APP_ENV') !== 'dev') {
            return $this->jsonResponse(false, "This endpoint is only available in development environment", 403);
        }
        
        try {
            $user = $this->userService->beginSecureUpdate($id);
            if (!$user) {
                return $this->jsonResponse(false, "User not found", 404);
            }

            sleep(15);

            $this->userService->completeUpdate();
        } catch(Exception $e) {
            error_log("Error in lock test: " . $e->getMessage());
            return $this->jsonResponse(false, "Could not acquire lock: " . $e->getMessage(), 423);
        }

        return $this->jsonResponse(true, "Lock test completed");
    }
}