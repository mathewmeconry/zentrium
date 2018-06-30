<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\GripPubControl;
use GripControl\WebSocketMessageFormat;
use PubControl\Item;

class GripPublisher
{
    private $grip;

    public function __construct(GripPubControl $grip)
    {
        $this->grip = $grip;
    }

    public function publish(string $channel, array $message)
    {
        $this->grip->publish($channel, new Item(new WebSocketMessageFormat(json_encode($message))));
    }
}
