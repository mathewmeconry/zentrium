<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
     * @Assert\Expression("this.getBegin() <= this.getEnd()", message="This value is not a valid time.")
     *
     * @ORM\Column(type="datetime")
     */
    protected $end;

    /**
     * @var Period
     */
    protected $period;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Requirement", mappedBy="set", cascade="ALL", orphanRemoval=true)
     */
    protected $requirements;

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

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }
}
