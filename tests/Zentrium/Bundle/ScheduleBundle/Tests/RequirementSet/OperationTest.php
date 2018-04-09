<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\RequirementSet;

use DateTime;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;

abstract class OperationTest extends TestCase
{
    protected function createTask($id)
    {
        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn($id);

        return $task;
    }

    protected function addRequirement(RequirementSet $set, Task $task, $from, $to, $count)
    {
        $requirement = new Requirement();
        $requirement->setTask($task);
        $requirement->setFrom(new DateTime($from));
        $requirement->setTo(new DateTime($to));
        $requirement->setCount($count);
        $requirement->setSet($set);
        $set->getRequirements()->add($requirement);
    }

    protected function assertRequirement(Collection $requirements, Task $task, $from, $to, $count)
    {
        $from = new DateTime($from);
        $to = new DateTime($to);
        $matching = $requirements->filter(function (Requirement $requirement) use ($task, $from, $to, $count) {
            if ($requirement->getTask()->getId() !== $task->getId()) {
                return false;
            }
            if ($requirement->getFrom() != $from || $requirement->getTo() != $to) {
                return false;
            }
            if ($requirement->getCount() !== $count) {
                return false;
            }

            return true;
        });

        $this->assertCount(1, $matching);
    }
}
