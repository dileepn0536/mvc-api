<?php

namespace Dileep\Mvc\Interfaces;

interface UserServiceInterface
{
    public function getUsers(int $limit = 20, int $offset = 0): array;
    public function createUser(string $name, string $email): bool;
    public function getUserById(?int $id): mixed;
    public function beginSecureUpdate(?int $id): mixed;
    public function completeUpdate(): bool;
    public function updateUser(?int $id, string $name, string $email): bool;
    public function deleteUser(?int $id): bool;
}