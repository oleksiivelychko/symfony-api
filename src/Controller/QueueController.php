<?php

namespace App\Controller;

use App\Contract\BrokerMessageContract;
use App\Message\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('queue', name: 'queue')]
final class QueueController extends AbstractController implements BrokerMessageContract
{
    #[Route('/test', name: '_send', methods: ['GET'])]
    public function testMessage(MessageBusInterface $messageBus): Response
    {
        $message = new Notification('Hello, World!');
        $messageBus->dispatch($message);

        return new Response(sprintf("Test message [%s] was sent", $message->getContent()));
    }
}