<?php
namespace Dileep\Mvc\Services;

interface NotificationInterface
{
    public function send(string $to, string $message): bool;
}