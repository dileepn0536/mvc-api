<?php

class UserController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getUsers();

        $this->jsonResponse([
            'status' => true,
            'data' => $users,
        ]);
    }

    public function createUser()
    {
        require_once "views/createuser.php";
    }

    public function storeUser()
    {
        $data = $this->getJsonData();
     
        if ($data === null) {
            $this->jsonResponse([
                'status' => false,
                'message' => 'Invalid JSON input'
            ], 400);
        }
        
        $name = $data['name'] ?? "";
        $email = $data['email'] ?? "";

        if(empty($name) || empty($email)) {
            $this->jsonResponse(['status' =>false, 'message' => "Name and Email are required"], 400);
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['status' => false, 'message' => "Email is invalid"], 400);
        }

        try {
            $this->userService->createUser($name, $email);
            $this->jsonResponse([
                'status' => true,
                'message' => "User created successfully"
            ],201);
        } catch(Exception $e) {
            $this->jsonResponse([
                'status' => false,
                'message' => "Internal server error"
            ],500);
        }
    }

    public function getEditUser()
    {
        $id = $_GET['id'] ?? "";

        if(!$id) {
            return "User not found";
        }

        $userinfo = $this->userService->getUserById($id);

        if(!$userinfo) {
            echo "User not found";
            return;
        }
        
        require_once "views/getedituser.php";
    }

    public function updateUser()
    {
        $data = $this->getJsonData();
        
        if ($data === null) {
            $this->jsonResponse([
                'status' => false,
                'message' => 'Invalid JSON input'
            ], 400);
        }

        $name = $data['name'] ?? "";
        $email = $data['email'] ?? "";
        $id = $data['id'] ?? null;

        if(!$id) {
            $this->jsonResponse([
                'status' => false,
                'message' => "Invalid user"
            ], 400);
        }

        if (empty($name) || empty($email)) {
            $this->jsonResponse([
                'status' => false,
                'message' => "All fields are required"
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse([
                'status' => false,
                'message' => "Invalid email format"
            ], 400);
        }

        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            $this->jsonResponse([
                'status' => false,
                'message' => "User not found"
            ], 404);
        }

        try {
            $this->userService->updateUser($id, $name, $email);
            $this->jsonResponse([
                'status' => true,
                'message' => "User updated successfully"
            ]);
        } catch(Exception $e) {
            $this->jsonResponse([
                'status' => false,
                'message' => "Internal server error"
            ], 500);
        }
    }

    public function deleteUser()
    {
        $data = $this->getJsonData();
        if ($data === null) {
            $this->jsonResponse([
                'status' => false,
                'message' => 'Invalid JSON input'
            ], 400);
        }
        $id = $data['id'] ?? null;
        if(!$id) {
            $this->jsonResponse([
                'status' => false,
                'message' => "The id is invalid"
            ], 400);
        }
        $userinfo = $this->userService->getUserById($id);

        if (!$userinfo) {
            $this->jsonResponse([
                'status' => false,
                'message' => "User not found"
            ], 404);
        }

        try {
            $this->userService->deleteUser($id);
            $this->jsonResponse(['status' => true, 'message' => "User deleted successfully"], 200);
        } catch(Exception $e) {
            $this->jsonResponse(['status' => false, 'message' => "Internal server error"], 500);
        }
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header("Content-type: application/json");
        echo json_encode($data);
        exit;
    }

    private function getJsonData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }
}