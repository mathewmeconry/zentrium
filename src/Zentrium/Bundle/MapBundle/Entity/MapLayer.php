<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity({"map", "layer"})
 */
class MapLayer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Map
     *
     * @Assert\NotNull
     * @Assert\Valid
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="layers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $map;

    /**
     * @var Layer
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Layer")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $layer;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @var float
     *
     * @Assert\NotNull
     * @Assert\Range(min=0.0, max=1.0)
     *
     * @ORM\Column(type="float")
     */
    protected $opacity;

    /**
     * @var bool
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    public function __construct()
    {
        $this->position = -1;
        $this->opacity = 1.0;
        $this->enabled = true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    public function getLayer()
    {
        return $this->layer;
    }

    public function setLayer($layer)
    {
        $this->layer = $layer;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getOpacity()
    {
        return $this->opacity;
    }

    public function setOpacity($opacity)
    {
        $this->opacity = $opacity;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
