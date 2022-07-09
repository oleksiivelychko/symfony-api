<?php

namespace App\Message;

class Notification
{
    public function __construct(private readonly string $content) {}

    public function getContent(): string
    {
        return $this->content;
    }
}