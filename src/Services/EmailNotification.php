<?php
namespace Dileep\Mvc\Services;

use Dileep\Mvc\Services\NotificationInterface;

class EmailNotification implements NotificationInterface
{
    public function send(string $to, string $message): bool
    {
        fwrite(\STDERR, "=== EMAIL SEND CALLED: $to ===" . PHP_EOL);

        fwrite(\STDERR, "=== SEND METHOD CALLED ===" . PHP_EOL);
        fwrite(\STDERR, "Email sent to: $to | Message: $message" . PHP_EOL);
        return true;
    }
}