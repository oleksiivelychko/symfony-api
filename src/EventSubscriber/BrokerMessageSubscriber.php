<?php

namespace App\EventSubscriber;

use App\Contract\BrokerMessageContract;
use App\Exception\RabbitMQFailedException;
use App\Services\BrokerMessageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BrokerMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BrokerMessageService $brokerMessageService) {}

    /**
     * @throws RabbitMQFailedException
     */
    public function onKernelController(ControllerEvent $controllerEvent)
    {
        $controller = $controllerEvent->getController();

        /**
         * when a controller class defines multiple action methods, the controller
         * is returned as [$controllerInstance, 'methodName']
         */
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof BrokerMessageContract) {
            $isOkRabbitMqStatus = $this->brokerMessageService->isOkRabbitMQStatus();
            if ($isOkRabbitMqStatus === false) {
                throw new RabbitMQFailedException();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}