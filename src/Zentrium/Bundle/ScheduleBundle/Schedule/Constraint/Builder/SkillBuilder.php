<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Builder;

class SkillBuilder implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium_schedule.constraint.skill.name';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'zentrium_schedule.constraint.skill.description';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [];
    }
}
