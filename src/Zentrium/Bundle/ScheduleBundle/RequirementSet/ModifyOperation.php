<?php

namespace Zentrium\Bundle\ScheduleBundle\RequirementSet;

use CallbackFilterIterator;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Util\SlotUtil;

class ModifyOperation implements OperationInterface
{
    /**
     * @Assert\NotNull
     */
    private $task;

    /**
     * @Assert\NotNull
     */
    private $from;

    /**
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not a valid time.")
     */
    private $to;

    /**
     * @Assert\NotNull
     */
    private $modification;

    public function getTask()
    {
        return $this->task;
    }

    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    public function getModification()
    {
        return $this->modification;
    }

    public function setModification($modification)
    {
        $this->modification = $modification;

        return $this;
    }

    public function apply(RequirementSet $set)
    {
        $period = SlotUtil::cover($set->getBegin(), $set->getSlotDuration(), new Period($this->getFrom(), $this->getTo()));
        if (!$period->overlaps($set->getPeriod())) {
            return;
        }
        $period = $period->intersect($set->getPeriod());

        $task = $this->getTask();

        $requirement = new Requirement();
        $requirement->setSet($set);
        $requirement->setTask($task);
        $requirement->setFrom($period->getStartDate());
        $requirement->setTo($period->getEndDate());
        $requirement->setCount($this->getModification());
        $set->getRequirements()->add($requirement);

        $mergeOperation = new MergeOperation();
        $mergeOperation->setTask($task);
        $mergeOperation->merge($set, new CallbackFilterIterator($set->getRequirements()->getIterator(), function (Requirement $requirement) use ($task, $period) {
            return ($requirement->getTask()->getId() == $task->getId() && ($period->overlaps($requirement->getPeriod()) || $period->abuts($requirement->getPeriod())));
        }));
    }
}
