<?php

namespace App\Tests\Messenger;

use App\Handlers\NotificationHandler;
use App\Message\Notification;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerHandlerTest extends TestCase
{
    private MessageBusInterface|MockObject $messageBus;

    private LoggerInterface|MockObject $logger;

    private NotificationHandler $handler;

    private Notification $notification;

    public function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handler = new NotificationHandler($this->logger);
        $this->notification = new Notification('Hello, World!');
    }

    public function testMessageBusDispatch()
    {
        $this->messageBus->expects(self::exactly(1))
            ->method('dispatch')
            ->willReturn(new Envelope($this->notification));

        $this->messageBus->dispatch($this->notification);
    }

    public function testHandleMessage()
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Notification handler consumed the message.');

        $handler = $this->handler;
        $handler($this->notification);
    }
}