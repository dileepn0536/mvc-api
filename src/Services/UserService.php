<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Repositories\UserRepository;

class UserService
{
    private ?UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function getUsers(int $limit = 20, int $offset = 0): array
    {
        return $this->userRepository->getUsers($limit, $offset);
    }

    public function createUser(string $name, string $email): bool
    {
        return $this->userRepository->createUser($name, $email);
    }

    public function getUserById(?int $id): mixed
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUser(?int $id, string $name, string $email): bool
    {
        return $this->userRepository->updateUser($id, $name, $email);
    }

    public function deleteUser(?int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }

    public function beginSecureUpdate(?int $id): mixed
    {
        return $this->userRepository->beginSecureUpdate($id);
    }

    public function completeUpdate(): bool
    {
        return $this->userRepository->commitSecureUpdate();
    }
}