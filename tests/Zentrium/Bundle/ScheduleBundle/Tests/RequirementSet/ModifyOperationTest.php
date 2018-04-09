<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\RequirementSet;

use DateTime;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\ModifyOperation;

class ModifyOperationTest extends OperationTest
{
    public function testApply()
    {
        $task1 = $this->createTask(1);
        $task2 = $this->createTask(2);

        $set = new RequirementSet();
        $set->setBegin(new DateTime('2015-01-01'));
        $set->setEnd(new DateTime('2015-01-02'));
        $set->setSlotDuration(3600);
        $this->addRequirement($set, $task1, '2015-01-01 10:00', '2015-01-01 14:00', 20);
        $this->addRequirement($set, $task1, '2015-01-01 12:00', '2015-01-01 16:00', 10);
        $this->addRequirement($set, $task2, '2015-01-01 09:00', '2015-01-01 17:00', 1);

        $operation = new ModifyOperation();
        $operation->setTask($task1);
        $operation->setFrom(new DateTime('2015-01-01 11:30'));
        $operation->setTo(new DateTime('2015-01-01 14:30'));
        $operation->setModification(-15);
        $operation->apply($set);

        $requirements = $set->getRequirements();
        $this->assertCount(5, $requirements);
        $this->assertRequirement($requirements, $task1, '2015-01-01 10:00', '2015-01-01 11:00', 20);
        $this->assertRequirement($requirements, $task1, '2015-01-01 11:00', '2015-01-01 12:00', 5);
        $this->assertRequirement($requirements, $task1, '2015-01-01 12:00', '2015-01-01 14:00', 15);
        $this->assertRequirement($requirements, $task1, '2015-01-01 15:00', '2015-01-01 16:00', 10);
        $this->assertRequirement($requirements, $task2, '2015-01-01 09:00', '2015-01-01 17:00', 1);
    }
}
