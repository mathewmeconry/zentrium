<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RequirementSet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $begin;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("this.getEnd() === null || (this.getBegin() <= this.getEnd() && this.isAligned(this.getEnd())", message="This value is not a valid time.")
     *
     * @ORM\Column(type="datetime")
     */
    protected $end;

    /**
     * @var Period
     */
    protected $period;

    /**
     * @var int
     *
     * @Assert\NotNull
     * @Assert\Range(min=60)
     *
     * @ORM\Column(type="integer")
     */
    protected $slotDuration;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Requirement", mappedBy="set", cascade="ALL", orphanRemoval=true)
     */
    protected $requirements;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getBegin()
    {
        return $this->begin;
    }

    public function setBegin($begin)
    {
        $this->begin = $begin;
        $this->period = null;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->end = $end;
        $this->period = null;

        return $this;
    }

    public function getPeriod()
    {
        if ($this->period === null && $this->begin !== null && $this->end !== null) {
            $this->period = new Period($this->begin, $this->end);
        }

        return $this->period;
    }

    public function getSlotDuration()
    {
        return $this->slotDuration;
    }

    public function setSlotDuration($slotDuration)
    {
        $this->slotDuration = $slotDuration;

        return $this;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    public function getSlotCount()
    {
        return ($this->end->getTimestamp() - $this->begin->getTimestamp()) / $this->slotDuration;
    }

    public function isAligned(DateTime $time)
    {
        if ($this->begin === null || $this->slotDuration === null) {
            return true;
        }

        $diff = $time->getTimestamp() - $this->begin->getTimestamp();

        return ($diff % $this->slotDuration == 0);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function update()
    {
        $this->updated = new DateTime();
    }
}
