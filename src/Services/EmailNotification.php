<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Services\NotificationInterface;

class EmailNotification implements NotificationInterface
{
    public function send(string $to, string $message): bool
    {
        error_log("=== EMAIL SEND CALLED: $to ===");

        error_log("=== SEND METHOD CALLED ===");
        error_log("Email sent to: $to | Message: $message");
        return true;
    }
}