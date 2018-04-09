<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\RequirementSet;

use DateTime;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\SetOperation;

class SetOperationTest extends OperationTest
{
    public function testApply()
    {
        $task1 = $this->createTask(1);
        $task2 = $this->createTask(2);

        $set = new RequirementSet();
        $set->setBegin(new DateTime('2015-01-01'));
        $set->setEnd(new DateTime('2015-01-02'));
        $set->setSlotDuration(3600);
        $this->addRequirement($set, $task1, '2015-01-01 10:00', '2015-01-01 14:00', 1);
        $this->addRequirement($set, $task1, '2015-01-01 12:00', '2015-01-01 13:00', 2);
        $this->addRequirement($set, $task2, '2015-01-01 09:00', '2015-01-01 17:00', 1);

        $operation = new SetOperation();
        $operation->setTask($task1);
        $operation->setFrom(new DateTime('2015-01-01 11:30'));
        $operation->setTo(new DateTime('2015-01-01 14:30'));
        $operation->setCount(5);
        $operation->apply($set);

        $requirements = $set->getRequirements();
        $this->assertCount(3, $requirements);
        $this->assertRequirement($requirements, $task1, '2015-01-01 10:00', '2015-01-01 11:00', 1);
        $this->assertRequirement($requirements, $task1, '2015-01-01 11:00', '2015-01-01 15:00', 5);
        $this->assertRequirement($requirements, $task2, '2015-01-01 09:00', '2015-01-01 17:00', 1);
    }

    public function testApplyZero()
    {
        $task1 = $this->createTask(1);
        $task2 = $this->createTask(2);

        $set = new RequirementSet();
        $set->setBegin(new DateTime('2015-01-01'));
        $set->setEnd(new DateTime('2015-01-02'));
        $set->setSlotDuration(3600);
        $this->addRequirement($set, $task1, '2015-01-01 10:00', '2015-01-01 11:00', 1);
        $this->addRequirement($set, $task1, '2015-01-01 09:00', '2015-01-01 12:00', 1);

        $operation = new SetOperation();
        $operation->setTask($task1);
        $operation->setFrom(new DateTime('2015-01-01 09:00'));
        $operation->setTo(new DateTime('2015-01-01 12:00'));
        $operation->setCount(0);
        $operation->apply($set);

        $requirements = $set->getRequirements();
        $this->assertCount(0, $requirements);
    }
}
