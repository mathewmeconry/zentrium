<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Availability
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(name="from_", type="datetime")
     */
    protected $from;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not valid.")
     *
     * @ORM\Column(name="to_", type="datetime")
     */
    protected $to;

    /**
     * @var Period
     */
    protected $period;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
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
}
