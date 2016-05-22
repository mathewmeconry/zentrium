<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Feature
{
    const TYPE_POINT = 'Point';
    const TYPE_LINESTRING = 'LineString';
    const TYPE_POLYGON = 'Polygon';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Choice(callback="getTypes")
     *
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var Layer
     *
     * @Assert\NotNull
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="FeatureLayer", inversedBy="features")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $layer;

    /**
     * @var array
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="json_array")
     */
    protected $coordinates;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $attributes;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

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

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public static function getTypes()
    {
        return [
            TYPE_POINT,
            TYPE_LINESTRING,
            TYPE_POLYGON,
        ];
    }
}
