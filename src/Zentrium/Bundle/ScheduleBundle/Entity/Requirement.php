<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Requirement extends AbstractPlanItem
{
    /**
     * @var RequirementSet
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="RequirementSet", inversedBy="requirements")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $set;

    /**
     * @var Task
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $task;

    /**
     * @var int
     *
     * @Assert\NotNull
     * @Assert\Range(min=0)
     *
     * @ORM\Column(type="integer")
     */
    protected $count;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPlan()
    {
        return $this->getSet();
    }

    public function getSet()
    {
        return $this->set;
    }

    public function setSet($set)
    {
        $this->set = $set;

        return $this;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setTask($task)
    {
        $this->task = $task;

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

    public function copy()
    {
        $copy = parent::copy();
        $copy->setTask($this->getTask());
        $copy->setCount($this->getCount());

        return $copy;
    }
}
