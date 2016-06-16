<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;

interface ValidatorInterface
{
    /**
     * Validates the given schedule and returns a list of messages in case
     * the schedule does not satisfy the constraint.
     *
     * @param Schedule            $schedule
     * @param ConstraintInterface $constraint
     *
     * @return MessageInterface[]
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint);
}
