<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 */
class Shift extends AbstractPlanItem
{
    /**
     * @var Schedule
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="shifts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $schedule;

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
     * @var BaseUser
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPlan()
    {
        return $this->getSchedule();
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
