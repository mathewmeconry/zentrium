<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Requirement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not a valid time.")
     * @Assert\Expression("this.getSet() === null || this.getSet().getBegin() <= this.getFrom()", message="This value is not a valid time.")
     *
     * @ORM\Column(type="datetime")
     */
    protected $from;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not a valid time.")
     * @Assert\Expression("this.getSet() === null || this.getSet().getEnd() >= this.getTo()", message="This value is not a valid time.")
     *
     * @ORM\Column(type="datetime")
     */
    protected $to;

    /**
     * @var Period
     */
    protected $period;

    /**
     * @var int
     *
     * @Assert\NotNull
     * @Assert\Range(min=0)
     *
     * @ORM\Column(type="integer")
     */
    protected $count;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
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

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
        $this->period = null;

        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
        $this->period = null;

        return $this;
    }

    public function getPeriod()
    {
        if ($this->period === null && $this->from !== null && $this->to !== null) {
            $this->period = new Period($this->from, $this->to);
        }

        return $this->period;
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

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }
}
