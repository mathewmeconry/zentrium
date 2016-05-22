<?php

namespace Zentrium\Bundle\MapBundle\Position;

use Symfony\Component\EventDispatcher\Event;
use Zentrium\Bundle\MapBundle\Entity\Position;

class PositionUpdateEvent extends Event
{
    /**
     * @var Position
     */
    private $position;

    public function __construct(Position $position)
    {
        $this->position = $position;
    }

    /**
     * @return Position
     */
    public function getPosition()
    {
        return $this->position;
    }
}
