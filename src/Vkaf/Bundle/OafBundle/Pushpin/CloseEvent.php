<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

use GripControl\WebSocketEvent;

class CloseEvent extends WebSocketEvent
{
    private $code;

    public function __construct($code = 0)
    {
        if ($code !== false) {
            parent::__construct('CLOSE', pack('n', $code));
        } else {
            parent::__construct('DISCONNECT');
        }

        $this->code = $code;
    }

    public function getCode()
    {
        return $code;
    }

    public function isClean()
    {
        return $this->code !== false;
    }
}
