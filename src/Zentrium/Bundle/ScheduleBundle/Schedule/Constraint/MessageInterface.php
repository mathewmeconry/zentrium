<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

use League\Period\Period;

interface MessageInterface
{
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_WARNING = 'warning';
    const LEVEL_INFO = 'info';

    /**
     * Returns the constraint which caused this message.
     *
     * @return ConstraintInterface
     */
    public function getConstraint();

    /**
     * Returns the severity level of the message.
     *
     * @return string
     */
    public function getLevel();

    /**
     * Gets the message ID.
     *
     * @return string
     */
    public function getMessageKey();

    /**
     * Returns any additional message parameters.
     *
     * @return array
     */
    public function getMessageParameters();

    /**
     * Returns a list of all involved elements.
     *
     * @return array
     */
    public function getElements();

    /**
     * Returns a Period in case the message is limited to a certain time period.
     *
     * @return Period|null
     */
    public function getPeriod();
}
