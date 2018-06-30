<?php

namespace Vkaf\Bundle\OafBundle\Pushpin;

class GripRequest
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function getEvents()
    {
        return $this->events;
    }
}
