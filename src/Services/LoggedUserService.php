<?php

namespace Dileep\Mvc\Services;

use Dileep\Mvc\Interfaces\UserServiceInterface;

class LoggedUserService implements UserServiceInterface
{
    public function __construct(
        private UserServiceInterface $service,
        private \Psr\Log\LoggerInterface $logger
    ) {}

    public function getUsers(int $limit = 20, int $offset = 0): array
    {
        $this->logger->info("Fetching users", [
            'limit'  => $limit,
            'offset' => $offset
        ]);

        $start  = microtime(true);
        $result = $this->service->getUsers($limit, $offset);
        $time   = round((microtime(true) - $start) * 1000, 2);

        $this->logger->info("Fetched users", [
            'count' => count($result),
            'time_ms' => $time
        ]);

        return $result;
    }

    public function getUserById(?int $id): mixed
    {
        $this->logger->info("Fetching user by ID", ['id' => $id]);

        $result = $this->service->getUserById($id);

        if (!$result) {
            $this->logger->warning("User not found", ['id' => $id]);
        }

        return $result;
    }

    public function createUser(string $name, string $email): bool
    {
        $this->logger->info("Creating user", [
            'name'  => $name,
            'email' => $email
        ]);

        $result = $this->service->createUser($name, $email);

        if ($result) {
            $this->logger->info("User created successfully", ['email' => $email]);
        } else {
            $this->logger->error("Failed to create user", ['email' => $email]);
        }

        return $result;
    }

    public function updateUser(?int $id, string $name, string $email): bool
    {
        $this->logger->info("Updating user", ['id' => $id]);

        $result = $this->service->updateUser($id, $name, $email);

        if ($result) {
            $this->logger->info("User updated successfully", ['id' => $id]);
        } else {
            $this->logger->error("Failed to update user", ['id' => $id]);
        }

        return $result;
    }

    public function deleteUser(?int $id): bool
    {
        $this->logger->warning("Deleting user", ['id' => $id]);

        $result = $this->service->deleteUser($id);

        if ($result) {
            $this->logger->info("User deleted successfully", ['id' => $id]);
        } else {
            $this->logger->error("Failed to delete user", ['id' => $id]);
        }

        return $result;
    }

    public function beginSecureUpdate(?int $id): mixed
    {
        $this->logger->info("Beginning secure update", ['id' => $id]);
        return $this->service->beginSecureUpdate($id);
    }

    public function completeUpdate(): bool
    {
        $this->logger->info("Completing secure update");
        return $this->service->completeUpdate();
    }
}