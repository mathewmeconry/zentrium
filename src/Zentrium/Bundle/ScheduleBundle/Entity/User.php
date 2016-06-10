<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="schedule_user")
 */
class User
{
    /**
     * @var BaseUser
     *
     * @Assert\Valid
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="id", onDelete="CASCADE")
     */
    protected $base;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="users")
     */
    protected $skills;

    /**
     * @ORM\OneToMany(targetEntity="Availability", mappedBy="user", cascade="all")
     * @ORM\OrderBy({"from"="ASC"})
     */
    protected $availabilities;

    public function __construct(BaseUser $base)
    {
        $this->base = $base;
        $this->skills = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    public function getBase()
    {
        return $this->base;
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

    public function getSkills()
    {
        return $this->skills;
    }

    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    public function getAvailabilities()
    {
        return $this->availabilities;
    }

    public function setAvailabilities($availabilities)
    {
        $this->availabilities = $availabilities;

        return $this;
    }
}
