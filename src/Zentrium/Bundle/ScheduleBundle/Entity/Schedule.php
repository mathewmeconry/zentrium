<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Schedule extends AbstractPlan
{
    /**
     * @var Collection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="schedule", cascade="ALL", orphanRemoval=true)
     */
    protected $shifts;

    /**
     * @var bool
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="boolean")
     */
    protected $published;

    public function __construct()
    {
        parent::__construct();

        $this->shifts = new ArrayCollection();
        $this->published = false;
    }

    public function getShifts()
    {
        return $this->shifts;
    }

    public function setShifts($shifts)
    {
        $this->shifts = $shifts;

        return $this;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
}
