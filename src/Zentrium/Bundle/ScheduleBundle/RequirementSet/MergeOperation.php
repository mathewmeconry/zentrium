<?php

namespace Zentrium\Bundle\ScheduleBundle\RequirementSet;

use ArrayIterator;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;
use Traversable;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Util\PeriodQueue;

class MergeOperation implements OperationInterface
{
    /**
     * @Assert\NotNull
     */
    private $task;

    public function getTask()
    {
        return $this->task;
    }

    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    public function apply(RequirementSet $set)
    {
        $task = $this->getTask();
        $this->merge($set, new CallbackFilterIterator($set->getRequirements()->getIterator(), function (Requirement $requirement) use ($task) {
            return ($requirement->getTask()->getId() == $task->getId());
        }));
    }

    /**
     * @param RequirementSet    $set
     * @param array|Traversable $requirements
     */
    public function merge(RequirementSet $set, $requirements)
    {
        $result = new ArrayIterator();

        $queue = new PeriodQueue();
        foreach ($requirements as $key => $requirement) {
            $queue->insert([$key, $requirement], $requirement->getPeriod());
        }

        if ($queue->isEmpty()) {
            return $result;
        }

        list($activeKey, $active) = $queue->extract();
        $result[$activeKey] = $active;
        while ($queue->valid()) {
            list($topKey, $top) = $queue->extract();
            $result[$topKey] = $top;
            if ($active->getTo() <= $top->getFrom()) {
                $active = $top;
                $activeKey = $topKey;
            } elseif ($active->getFrom() == $top->getFrom()) {
                if ($active->getTo() > $top->getTo()) {
                    $remaining = new Period($top->getTo(), $active->getTo());
                    $queue->insert($this->addRequirement($set, $remaining, $active->getCount()), $remaining);
                    $active->setTo($top->getTo());
                } elseif ($active->getTo() < $top->getTo()) {
                    $remaining = new Period($active->getTo(), $top->getTo());
                    $queue->insert($this->addRequirement($set, $remaining, $top->getCount()), $remaining);
                    $active->setTo($active->getTo());
                }
                $active->setCount($active->getCount() + $top->getCount());
                unset($result[$topKey]);
                $set->getRequirements()->remove($topKey);
            } else {
                $remaining = new Period($top->getFrom(), $active->getTo());
                $active->setTo($top->getFrom());
                $queue->insert($this->addRequirement($set, $remaining, $active->getCount()), $remaining);
                $queue->insert([$topKey, $top], $top->getPeriod());
            }
        }

        return $this->clean($set, $this->concat($set, $result));
    }

    private function concat(RequirementSet $set, ArrayIterator $it)
    {
        $it->uasort(function ($first, $second) {
            if ($first->getFrom() == $second->getFrom()) {
                return 0;
            }

            return ($first->getFrom() < $second->getFrom() ? -1 : 1);
        });

        $result = new ArrayIterator();
        $previous = null;
        foreach ($it as $key => $current) {
            if ($previous !== null && $previous->getTo() == $current->getFrom() && $previous->getCount() == $current->getCount()) {
                $previous->setTo($current->getTo());
                $set->getRequirements()->remove($key);
            } else {
                $previous = $current;
                $result[$key] = $current;
            }
        }

        return $result;
    }

    private function clean(RequirementSet $set, ArrayIterator $it)
    {
        $result = new ArrayIterator();
        foreach ($it as $key => $requirement) {
            if ($requirement->getCount() > 0 && $requirement->getTo() > $requirement->getFrom()) {
                $result[$key] = $requirement;
            } else {
                $set->getRequirements()->remove($key);
            }
        }

        return $result;
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
