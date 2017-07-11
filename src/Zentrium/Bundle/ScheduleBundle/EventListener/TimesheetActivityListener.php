<?php

namespace Zentrium\Bundle\ScheduleBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;
use Zentrium\Bundle\TimesheetBundle\Entity\Activity;

class TimesheetActivityListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        if ($metadata->getName() !== Task::class) {
            return;
        }

        $builder = new ClassMetadataBuilder($metadata);
        $builder->createManyToOne('timesheetActivity', Activity::class)
            ->addJoinColumn(null, null, true, false, 'SET NULL')
            ->build()
        ;
    }
}
