<?php

namespace Dileep\Mvc\Interfaces;

interface UserCrudInterface
{
    public function updateUser(?int $id, string $name, string $email): bool;

    public function deleteUser(?int $id): bool;

    public function getUserById(?int $id): mixed;

    public function createUser(string $name, string $email): bool;

    public function getUsers(int $limit = 20, int $offset = 0): array;
}