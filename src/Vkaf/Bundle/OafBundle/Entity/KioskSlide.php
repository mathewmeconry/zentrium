<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="oaf_kiosk_slide")
 */
class KioskSlide
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Kiosk
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Kiosk", inversedBy="slides")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $kiosk;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string", length=20)
     */
    protected $type;

    /**
     * @var array
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="json_array")
     */
    protected $options;

    /**
     * @var int
     *
     * @Assert\NotNull
     * @Assert\Range(min=1)
     *
     * @ORM\Column(type="integer")
     */
    protected $duration;

    /**
     * @var bool
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="boolean")
     */
    protected $hidden;

    /**
     * @var int
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="integer")
     */
    protected $order;

    public function __construct()
    {
        $this->hidden = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKiosk()
    {
        return $this->kiosk;
    }

    public function setKiosk($kiosk)
    {
        $this->kiosk = $kiosk;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    public function isHidden()
    {
        return $this->hidden;
    }

    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}
