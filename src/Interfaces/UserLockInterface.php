<?php

namespace Dileep\Mvc\Interfaces;

interface UserLockInterface
{
    public function beginSecureUpdate(?int $id): mixed;
    public function completeUpdate(): bool;
}