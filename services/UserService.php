<?php
class UserService
{
    private $userRepository;
    private $cache;

    public function __construct(
        UserRepository $userRepository,
        Cache $cache
    ) {
        $this->userRepository = $userRepository;
        $this->cache = $cache;
    }

    public function getUsers($limit = 20, $offset = 0)
    {
        $cacheKey = "users_{$limit}_{$offset}";

        // check cache first
        $users = $this->cache->get($cacheKey);

        if ($users === null) {
            // cache miss → hit DB
            $users = $this->userRepository->getUsers($limit, $offset);
            // store in cache for 5 minutes
            $this->cache->set($cacheKey, $users, 300);
        }

        return $users;
    }

    public function createUser($name, $email)
    {
        $result = $this->userRepository->createUser($name, $email);
        if($result) {
            // send welcome email
            $notification = NotificationFactory::create('email');
            $notification->send(
                $email,
                "Welcome $name! Your account has been created."
            );
            $this->cache->flush(); // invalidate cache ✅
        }
        return $result;
    }

    public function getUserById($id)
    {
        $cacheKey = "user_{$id}";

        $user = $this->cache->get($cacheKey);

        if ($user === null) {
            $user = $this->userRepository->getUserById($id);
            if ($user) {
                $this->cache->set($cacheKey, $user, 300);
            }
        }

        return $user;
    }

    public function updateUser($id, $name, $email)
    {
        $result = $this->userRepository->updateUser($id, $name, $email);
        $this->cache->flush(); // invalidate cache ✅
        return $result;
    }

    public function deleteUser($id)
    {
        $result = $this->userRepository->deleteUser($id);
        $this->cache->flush(); // invalidate cache ✅
        return $result;
    }
}