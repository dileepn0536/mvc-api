<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Services\NotificationInterface;

class SMSNotification implements NotificationInterface
{
    public function send(string $to, string $message): bool
    {
        // in real project → use Twilio API
        error_log("SMS sent to: $to | Message: $message");
        return true;
    }
}