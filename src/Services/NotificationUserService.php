<?php

namespace Dileep\Mvc\Services;

use Dileep\Mvc\Interfaces\UserServiceInterface;
use Dileep\Mvc\Services\NotificationFactory;

class NotificationUserService implements UserServiceInterface
{
    public function __construct(
        private UserServiceInterface $service
    ) {}

    public function createUser(string $name, string $email): bool
    {
        error_log("=== NOTIFICATION CREATE USER CALLED ===");

        $result = $this->service->createUser($name, $email);
        if ($result) {
            // notification responsibility is HERE now, not in UserService
            $notification = NotificationFactory::create('email');
            $notification->send(
                $email,
                "Welcome $name! Your account has been created."
            );
        }
        return $result;
    }

    public function getUsers(int $limit = 20, int $offset = 0): array
    {
        return $this->service->getUsers($limit, $offset);
    }

    public function getUserById(?int $id): mixed
    {
        return $this->service->getUserById($id);
    }

    public function updateUser(?int $id, string $name, string $email): bool
    {
        return $this->service->updateUser($id, $name, $email);
    }

    public function deleteUser(?int $id): bool
    {
        return $this->service->deleteUser($id);
    }

    public function beginSecureUpdate(?int $id): mixed
    {
        return $this->service->beginSecureUpdate($id);
    }

    public function completeUpdate(): bool
    {
        return $this->service->completeUpdate();
    }
}