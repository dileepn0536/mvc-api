<?php

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