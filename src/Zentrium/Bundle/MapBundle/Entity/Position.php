<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Position
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
     * @Serializer\Type("string")
     *
     * @ORM\Column(type="string")
     */
    protected $device;

    /**
     * @var float
     *
     * @Assert\NotNull
     * @Assert\Range(min=-90.0, max=90.0)
     * @Serializer\Type("float")
     *
     * @ORM\Column(type="float")
     */
    protected $latitude;

    /**
     * @var float
     *
     * @Assert\NotNull
     * @Assert\Range(min=-180.0, max=180.0)
     * @Serializer\Type("float")
     *
     * @ORM\Column(type="float")
     */
    protected $longitude;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:sP'>")
     *
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:sP'>")
     * @Serializer\ReadOnly
     *
     * @ORM\Column(type="datetime")
     */
    protected $serverTime;

    /**
     * @var array
     *
     * @Serializer\Type("array<string, string>")
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $attributes;

    public function getId()
    {
        return $this->id;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    public function getServerTime()
    {
        return $this->serverTime;
    }

    public function setServerTime($serverTime)
    {
        $this->serverTime = $serverTime;

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

    /**
     * @Serializer\PostDeserialize
     */
    public function updateServerTime()
    {
        if ($this->serverTime === null) {
            $this->serverTime = new \DateTime();
        }
    }
}
