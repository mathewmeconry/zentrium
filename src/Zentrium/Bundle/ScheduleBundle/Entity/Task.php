<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Validator\Constraints as AssertCore;

/**
 * @ORM\Entity
 * @UniqueEntity("code")
 */
class Task
{
    const DEFAULT_COLOR = '#cccccc';

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
     * @Assert\Length(max=5)
     * @Assert\Regex(pattern="/^[A-Z0-9.-]+([.-][A-Z0-9]+)*$/i")
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @AssertCore\Color
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $color;

    public function __construct()
    {
        $this->color = self::DEFAULT_COLOR;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;

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

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }
}
