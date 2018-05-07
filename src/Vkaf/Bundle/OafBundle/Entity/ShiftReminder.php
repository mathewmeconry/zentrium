<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Vkaf\Bundle\OafBundle\Entity\ShiftReminderRepository")
 * @ORM\Table(name="oaf_shift_reminder")
 */
class ShiftReminder
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
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
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
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    public function __construct(User $user, DateTime $from)
    {
        $this->user = $user;
        $this->from = $from;
        $this->created = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
