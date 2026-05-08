<?php

namespace Dileep\Mvc\Interfaces;

use Dileep\Mvc\Interfaces\UserCrudInterface;
use Dileep\Mvc\Interfaces\UserLockInterface;

interface UserServiceInterface extends UserCrudInterface, UserLockInterface
{
}