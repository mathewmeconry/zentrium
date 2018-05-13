<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Vkaf\Bundle\OafBundle\Entity\MessageRepository")
 * @ORM\Table("oaf_message")
 */
class Message
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
    protected $text;

    /**
     * @ORM\OneToMany(targetEntity="MessageDelivery", mappedBy="message", cascade="all")
     * @ORM\OrderBy({"created"="DESC"})
     */
    protected $deliveries;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
        $this->created = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getDeliveries()
    {
        return $this->deliveries;
    }

    public function setDeliveries($deliveries)
    {
        $this->deliveries = $deliveries;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
