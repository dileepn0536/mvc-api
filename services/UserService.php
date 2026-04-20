<?php
class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers()
    {
        return $this->userRepository->getUsers();
    }

    public function createUser($name, $email)
    {
        return $this->userRepository->createUser($name, $email);
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUser($id, $name, $email)
    {
        return $this->userRepository->updateUser($id, $name, $email);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->deleteUser($id);
    }
}