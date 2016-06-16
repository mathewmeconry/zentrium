<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

class SeparationBuilder implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium_schedule.constraint.separation.name';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'zentrium_schedule.constraint.separation.description';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [];
    }
}
