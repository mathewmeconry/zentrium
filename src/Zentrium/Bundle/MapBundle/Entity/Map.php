<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Zentrium\Bundle\MapBundle\Entity\MapRepository")
 */
class Map
{
    const DEFAULT_ZOOM = 12;

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
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="MapLayer", mappedBy="map")
     */
    protected $layers;

    /**
     * @var float
     *
     * @Assert\NotNull
     * @Assert\Range(min=-90.0, max=90.0)
     *
     * @ORM\Column(type="float")
     */
    protected $centerLatitude;

    /**
     * @var float
     *
     * @Assert\NotNull
     * @Assert\Range(min=-180.0, max=180.0)
     *
     * @ORM\Column(type="float")
     */
    protected $centerLongitude;

    /**
     * @var int
     *
     * @Assert\NotNull
     * @Assert\Range(min=0, max=25)
     *
     * @ORM\Column(type="integer")
     */
    protected $zoom;

    /**
     * @var bool
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="boolean")
     */
    protected $default;

    public function __construct()
    {
        $this->layers = new ArrayCollection();
        $this->default = false;
        $this->zoom = self::DEFAULT_ZOOM;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getLayers()
    {
        return $this->layers;
    }

    public function setLayers($layers)
    {
        $this->layers = $layers;

        return $this;
    }

    public function getCenterLatitude()
    {
        return $this->centerLatitude;
    }

    public function setCenterLatitude($centerLatitude)
    {
        $this->centerLatitude = $centerLatitude;

        return $this;
    }

    public function getCenterLongitude()
    {
        return $this->centerLongitude;
    }

    public function setCenterLongitude($centerLongitude)
    {
        $this->centerLongitude = $centerLongitude;

        return $this;
    }

    public function getCenter()
    {
        return [$this->centerLongitude, $this->centerLatitude];
    }

    public function getZoom()
    {
        return $this->zoom;
    }

    public function setZoom($zoom)
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function isDefault()
    {
        return $this->default;
    }

    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }
}
