<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

interface BuilderInterface
{
    /**
     * Returns the name of the constraint.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the description of the constraint.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Initializes a new set of parameters.
     *
     * @return mixed
     */
    public function initialize();
}
