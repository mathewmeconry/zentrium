<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

interface ConstraintInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getParameters();
}
