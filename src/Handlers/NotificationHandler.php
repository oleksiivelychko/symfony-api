<?php

namespace App\Handlers;

use App\Message\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 *  It will be called when that message is dispatched.
 */
#[AsMessageHandler]
final class NotificationHandler implements MessageHandlerInterface
{
    public function __invoke(Notification $message)
    {
        $this->logger->info('Notification handler consumed the message.');
        // ... do some prolonged work.
    }

    public function __construct(private readonly LoggerInterface $logger) {}
}