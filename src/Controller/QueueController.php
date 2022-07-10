<?php

namespace App\Controller;

use App\Message\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('queue', name: 'queue')]
final class QueueController extends AbstractController
{
    #[Route('/test', name: '_send', methods: ['GET'])]
    public function testMessage(MessageBusInterface $bus): Response
    {
        $message = new Notification('Hello, World!');
        $bus->dispatch($message);

        return new Response(sprintf("Test message [%s] was sent", $message->getContent()));
    }
}