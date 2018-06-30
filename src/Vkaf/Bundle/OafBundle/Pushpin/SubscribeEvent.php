<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\WebSocketEvent;

class SubscribeEvent extends WebSocketEvent
{
    private $channel;

    public function __construct($channel)
    {
        parent::__construct('TEXT', 'c:'.json_encode([
            'type' => 'subscribe',
            'channel' => $channel,
        ]));

        $this->channel = $channel;
    }

    public function getChannel()
    {
        return $this->channel;
    }
}
