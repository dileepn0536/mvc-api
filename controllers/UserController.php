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
        // require_once "views/showuser.php";

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
        // $name = $_POST['name'] ?? "";
        // $email = $_POST['email'] ?? "";

        $data = $_POST ?: $this->getJsonData();
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
            // echo "Email is invalid";
            $this->jsonResponse(['status' => false, 'message' => "Email is invalid"], 400);
        }

        try {
             $this->userService->createUser($name, $email);
            // header("Location: /oops/mvc/users");
            // exit;
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
        // $name = $_POST['name'] ?? "";
        // $email = $_POST['email'] ?? "";
        // $id = $_POST['id'] ?? null;

        $data = $_POST ?: $this->getJsonData();
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
            // echo "Invalid user";
            $this->jsonResponse([
                'status' => false,
                'message' => "Invalid user"
            ], 400);
        }

        if (empty($name) || empty($email)) {
            // echo "All fields are required";
            $this->jsonResponse([
                'status' => false,
                'message' => "All fields are required"
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // echo "Invalid email format";
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
        // header("Location: /oops/mvc/users");
        // exit;
    }

    public function deleteUser()
    {
        // $id = $_GET['id'] ?? null;
        $data = $this->getJsonData();
        if ($data === null) {
            $this->jsonResponse([
                'status' => false,
                'message' => 'Invalid JSON input'
            ], 400);
            // return;
        }
        $id = $data['id'] ?? null;
        if(!$id) {
            $this->jsonResponse([
                'status' => false,
                'message' => "The id is invalid"
            ], 400);
            // return;
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

        // header("Location: /oops/mvc/users");

        // exit;
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