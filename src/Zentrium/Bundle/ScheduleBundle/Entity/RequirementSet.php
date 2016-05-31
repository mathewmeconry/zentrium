<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class RequirementSet extends AbstractPlan
{
    /**
     * @var Collection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Requirement", mappedBy="set", cascade="ALL", orphanRemoval=true)
     */
    protected $requirements;

    public function __construct()
    {
        parent::__construct();

        $this->requirements = new ArrayCollection();
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
