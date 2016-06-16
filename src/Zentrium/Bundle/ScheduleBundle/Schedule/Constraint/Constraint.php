<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

class Constraint implements ConstraintInterface
{
    private $type;
    private $name;
    private $parameters;

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $name
     * @param mixed  $parameters
     */
    public function __construct($type, $name, $parameters)
    {
        $this->type = $type;
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
