<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\WebSocketEvent;

class OpenEvent extends WebSocketEvent
{
    public function __construct()
    {
        parent::__construct('OPEN');
    }
}
