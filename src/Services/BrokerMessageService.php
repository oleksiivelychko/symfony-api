<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

final class BrokerMessageService
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function isOkRabbitMQStatus(): bool
    {
        try {
            $username = $_ENV['RABBITMQ_USERNAME'];
            $password = $_ENV['RABBITMQ_PASSWORD'];
            $vhost = $_ENV['RABBITMQ_VHOST'];
            $host = $_ENV['RABBITMQ_HOST'];
            $port = $_ENV['RABBITMQ_PORT'];

            $amqpConnection = new \AMQPConnection();
            $amqpConnection->setLogin($username);
            $amqpConnection->setPassword($password);
            $amqpConnection->setHost($host);
            $amqpConnection->setPort($port);
            $amqpConnection->setVhost($vhost);
            $amqpConnection->connect();
        } catch (\AMQPConnectionException $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }

        if (!$amqpConnection->isConnected()) {
            $this->logger->error('RabbitMQ could not connect.');
            return false;
        }

        $amqpConnection->disconnect();
        return true;
    }
}