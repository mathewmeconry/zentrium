<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\WebSocketEvent;

class MessageEvent extends WebSocketEvent
{
    private $message;

    public function __construct(array $message)
    {
        parent::__construct('TEXT', json_encode($message));

        $this->message = $message;
    }

    public function getMessage(): array
    {
        return $this->message;
    }
}
