<?php

namespace Zentrium\Bundle\TimesheetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\TimesheetBundle\Validator\Constraints as AssertTimesheet;

/**
 * @AssertTimesheet\NotOverlapping
 *
 * @ORM\Entity
 */
class Entry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Activity
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $activity;

    /**
     * @var User
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $start;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("value === null || this.getStart() <= value", message="This value is not a valid time.")
     *
     * @ORM\Column(type="datetime")
     */
    protected $end;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getActivity()
    {
        return $this->activity;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;

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

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    public function getDuration()
    {
        return $this->getEnd()->getTimestamp() - $this->getStart()->getTimestamp();
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
