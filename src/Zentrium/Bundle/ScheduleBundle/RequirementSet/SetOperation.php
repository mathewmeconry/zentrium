<?php

namespace Zentrium\Bundle\ScheduleBundle\RequirementSet;

use ArrayIterator;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Util\SlotUtil;

class SetOperation implements OperationInterface
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
    private $count;

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

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;

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

        $result = new ArrayIterator();
        foreach ($set->getRequirements() as $key => $requirement) {
            if ($requirement->getTask()->getId() !== $task->getId()) {
                continue;
            }
            if ($period->abuts($requirement->getPeriod())) {
                $result[$key] = $requirement;
            } elseif ($period->overlaps($requirement->getPeriod())) {
                $diffs = $period->diff($requirement->getPeriod());
                foreach ($diffs as $diff) {
                    if ($requirement->getPeriod()->contains($diff)) {
                        list($diffKey, $diffRequirement) = $this->addRequirement($set, $diff, $requirement->getCount());
                        $result[$diffKey] = $diffRequirement;
                    }
                }
                $set->getRequirements()->remove($key);
            }
        }
        list($opKey, $opRequirement) = $this->addRequirement($set, $period, $this->getCount());
        $result[$opKey] = $opRequirement;

        $mergeOperation = new MergeOperation();
        $mergeOperation->setTask($task);
        $mergeOperation->merge($set, $result);
    }

    private function addRequirement(RequirementSet $set, Period $period, $count)
    {
        $requirement = new Requirement();
        $requirement->setSet($set);
        $requirement->setTask($this->getTask());
        $requirement->setFrom($period->getStartDate());
        $requirement->setTo($period->getEndDate());
        $requirement->setCount($count);

        $set->getRequirements()->add($requirement);
        $set->getRequirements()->last();
        $key = $set->getRequirements()->key();

        return [$key, $requirement];
    }
}
