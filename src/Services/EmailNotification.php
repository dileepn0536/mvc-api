<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Services\NotificationInterface;

class EmailNotification implements NotificationInterface
{
    public function send(string $to, string $message): bool
    {
        // in real project → use PHPMailer or SMTP
        error_log("Email sent to: $to | Message: $message");
        return true;
    }
}