<?php

namespace Zentrium\Bundle\LogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Zentrium\Bundle\LogBundle\Entity\LogRepository")
 * @ORM\Table(name="logs")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    const STATUS_OPEN = 'open';
    const STATUS_ON_HOLD = 'onhold';
    const STATUS_DONE = 'done';
    const STATUS_INVALID = 'invalid';

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
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Choice(callback="getStatuses")
     *
     * @ORM\Column(type="string", length=20)
     */
    protected $status;

    /**
     * @ORM\ManyToMany(targetEntity="Label")
     */
    protected $labels;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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

    public function getLabels()
    {
        return $this->labels;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels;

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

    /**
     * @ORM\PreUpdate
     */
    public function update()
    {
        $this->updated = new \DateTime();
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_ON_HOLD,
            self::STATUS_DONE,
            self::STATUS_INVALID,
        ];
    }
}
