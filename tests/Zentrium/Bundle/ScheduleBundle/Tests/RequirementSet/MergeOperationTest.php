<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\RequirementSet;

use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\MergeOperation;

class MergeOperationTest extends OperationTest
{
    public function testApply()
    {
        $task1 = $this->createTask(1);
        $task2 = $this->createTask(2);

        $set = new RequirementSet();
        $this->addRequirement($set, $task1, '2015-01-01 09:00', '2015-01-01 12:00', 2);
        $this->addRequirement($set, $task1, '2015-01-01 12:00', '2015-01-01 15:00', 2);
        $this->addRequirement($set, $task1, '2015-01-01 13:00', '2015-01-01 14:00', 7);
        $this->addRequirement($set, $task2, '2015-01-01 09:00', '2015-01-01 15:00', 11);

        $operation = new MergeOperation();
        $operation->setTask($task1);
        $operation->apply($set);

        $requirements = $set->getRequirements();
        $this->assertCount(4, $requirements);
        $this->assertRequirement($requirements, $task1, '2015-01-01 09:00', '2015-01-01 13:00', 2);
        $this->assertRequirement($requirements, $task1, '2015-01-01 13:00', '2015-01-01 14:00', 9);
        $this->assertRequirement($requirements, $task1, '2015-01-01 14:00', '2015-01-01 15:00', 2);
        $this->assertRequirement($requirements, $task2, '2015-01-01 09:00', '2015-01-01 15:00', 11);
    }
}
