<?php

namespace Dileep\Mvc\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dileep\Mvc\Interfaces\UserServiceInterface;
use Dileep\Mvc\Repositories\UserRepository;
use Dileep\Mvc\Services\UserService;
use Dileep\Mvc\Services\CachedUserService;
use Dileep\Mvc\Services\NotificationUserService;
use Dileep\Mvc\Services\LoggedUserService;

class AppServiceProvider
{
    public function register(Container $container): void
    {
        // Bind PDO
        $container->bind(\PDO::class, function () {
            return Database::getInstance()->getConnection();
        });

        // Bind Cache
        $container->bind(Cache::class, function () {
            return new Cache();
        });

        // Bind UserServiceInterface — full decorator chain
        $container->bind(UserServiceInterface::class, function (Container $c) {
            // Layer 1 — DB only
            $core = new UserService(
                $c->resolve(UserRepository::class)
            );

            // Layer 2 — Cache
            $cached = new CachedUserService(
                $core,
                $c->resolve(Cache::class)
            );

            // Layer 3 — Notification
            $notification = new NotificationUserService($cached);

            // Layer 4 — Logging
            $logger = new Logger('app');
            $logger->pushHandler(
                new StreamHandler('php://stderr', Logger::DEBUG)
            );

            return new LoggedUserService($notification, $logger);
        });
    }
}