<?php

namespace Zentrium\Bundle\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Validator\Constraints as AssertCore;

/**
 * @ORM\Entity(repositoryClass="Zentrium\Bundle\LogBundle\Entity\LabelRepository")
 * @ORM\Table(name="log_labels")
 */
class Label
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
     * @ORM\Column(type="string", length=20)
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @AssertCore\Color
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $color;

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
