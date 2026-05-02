<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Services\EmailNotification;
use Dileep\Mvc\Services\NotificationInterface;
use Exception;
use Dileep\Mvc\Services\SMSNotification;

class NotificationFactory
{
    public static function create(string $type): NotificationInterface
    {
        return match($type) {
            'email' => new EmailNotification(),
            'sms'   => new SMSNotification(),
            default => throw new Exception(
                "Invalid notification type: $type"
            )
        };
    }
}