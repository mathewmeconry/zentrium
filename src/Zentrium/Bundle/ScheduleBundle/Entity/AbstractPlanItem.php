<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractPlanItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not a valid time.")
     * @Assert\Expression("this.getPlan() === null || this.getFrom() === null || (this.getPlan().getBegin() <= this.getFrom() && this.getPlan().isAligned(this.getFrom()))", message="This value is not a valid time.")
     *
     * @ORM\Column(name="from_", type="datetime")
     */
    protected $from;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getFrom() <= this.getTo()", message="This value is not a valid time.")
     * @Assert\Expression("this.getPlan() === null || this.getTo() === null || (this.getTo() <= this.getPlan().getEnd() && this.getPlan().isAligned(this.getTo()))", message="This value is not a valid time.")
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

    /**
     * @return AbstractPlan
     */
    abstract public function getPlan();

    public function getId()
    {
        return $this->id;
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

    protected function copy()
    {
        $copy = new static();
        $copy->setFrom(clone $this->getFrom());
        $copy->setTo(clone $this->getTo());

        return $copy;
    }
}
