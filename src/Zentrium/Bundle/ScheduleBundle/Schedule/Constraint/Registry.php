<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder\BuilderInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator\ValidatorInterface;

class Registry
{
    private $validators;
    private $builders;

    public function __construct()
    {
        $this->validators = [];
        $this->builders = [];
    }

    public function addValidator($type, ValidatorInterface $validator)
    {
        $this->validators[$type] = $validator;
    }

    public function getValidator($type)
    {
        if (!isset($this->validators[$type])) {
            return null;
        }

        return $this->validators[$type];
    }

    public function addBuilder($type, BuilderInterface $builder)
    {
        $this->builders[$type] = $builder;
    }

    public function getBuilder($type)
    {
        if (!isset($this->builders[$type])) {
            return null;
        }

        return $this->builders[$type];
    }

    public function getBuilders()
    {
        return $this->builders;
    }
}
