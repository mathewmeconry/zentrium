<?php

namespace Vkaf\Bundle\OafBundle\Terminal;

use Symfony\Component\EventDispatcher\Event;

class FlowEvent extends Event
{
    private $tag;
    private $message;
    private $context;

    public function __construct(string $tag, $message, array $context)
    {
        $this->tag = $tag;
        $this->message = $message;
        $this->context = $context;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
