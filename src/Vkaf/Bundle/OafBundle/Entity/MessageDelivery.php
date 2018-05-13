<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table("oaf_message_delivery")
 */
class MessageDelivery
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Message
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="deliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $message;

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
     * @var PhoneNumber
     *
     * @Assert\NotNull
     * @AssertPhoneNumber
     *
     * @ORM\Column(type="phone_number")
     */
    protected $number;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $extra;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct(Message $message, User $user, PhoneNumber $number)
    {
        $this->message = $message;
        $this->user = $user;
        $this->number = $number;
        $this->updated = $this->created = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function update()
    {
        $this->updated = new DateTime();

        return $this;
    }
}
