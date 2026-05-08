<?php

namespace Dileep\Mvc\Services;

use Dileep\Mvc\Interfaces\UserCrudInterface;
use Dileep\Mvc\Core\Cache;

class CachedUserService implements UserCrudInterface
{
    public function __construct(
        private UserCrudInterface $userService,
        private Cache $cache
    ) {

    }

    public function getUsers(int $limit = 20, int $offset = 0): array
    {
        $cacheKey = "users_{$limit}_{$offset}";
        // check cache first
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        // cache miss → hit DB
        $users = $this->userService->getUsers($limit, $offset);
        // store in cache for 5 minutes
        $this->cache->set($cacheKey, $users, 300);

        return $users;
    }

    public function createUser(string $name, string $email): bool
    {
        $result = $this->userService->createUser($name, $email);
        
        if ($result) {
            $this->cache->flush(); // invalidate cache ✅
        }

        return $result;
    }

    public function getUserById(?int $id): mixed
    {
        $key = "user_{$id}";
        $cached = $this->cache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $user = $this->userService->getUserById($id);
        if ($user) {
            $this->cache->set($key, $user, 300);
        }

        return $user;
    }

    public function updateUser(?int $id, string $name, string $email): bool
    {
        $result = $this->userService->updateUser($id, $name, $email);
        
        if ($result) {
            $this->cache->flush(); // invalidate cache ✅
        }

        return $result;
    }

    public function deleteUser(?int $id): bool
    {
        $result = $this->userService->deleteUser($id);
        
        if ($result) {
            $this->cache->flush(); // invalidate cache ✅
        }

        return $result;
    }
}