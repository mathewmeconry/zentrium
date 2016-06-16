<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

use League\Period\Period;

class Message implements MessageInterface
{
    private $constraint;
    private $level;
    private $messageKey;
    private $messageParameters;
    private $elements;
    private $period;

    public function __construct(ConstraintInterface $constraint, $level, $messageKey, $messageParameters = [], $elements = [], Period $period = null)
    {
        $this->constraint = $constraint;
        $this->level = $level;
        $this->messageKey = $messageKey;
        $this->messageParameters = $messageParameters;
        $this->elements = $elements;
        $this->period = $period;
    }

    public static function critical(ConstraintInterface $constraint, $messageKey, $messageParameters = [], $elements = [], Period $period = null)
    {
        return new static($constraint, self::LEVEL_CRITICAL, $messageKey, $messageParameters, $elements, $period);
    }

    public static function warning(ConstraintInterface $constraint, $messageKey, $messageParameters = [], $elements = [], Period $period = null)
    {
        return new static($constraint, self::LEVEL_WARNING, $messageKey, $messageParameters, $elements, $period);
    }

    public static function info(ConstraintInterface $constraint, $messageKey, $messageParameters = [], $elements = [], Period $period = null)
    {
        return new static($constraint, self::LEVEL_INFO, $messageKey, $messageParameters, $elements, $period);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return $this->messageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
