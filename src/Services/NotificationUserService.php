<?php

namespace Dileep\Mvc\Services;

use Dileep\Mvc\Interfaces\UserServiceInterface;
use Dileep\Mvc\Services\NotificationFactory;

class NotificationUserService 
{
    public function __construct(
        private UserServiceInterface $service
    ) {}

    public function createUser(string $name, string $email): bool
    {
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
}