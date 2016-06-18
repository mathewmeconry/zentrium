<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;

/**
 * @ORM\Entity
 */
class TextWidget
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=50)
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"Zentrium\Bundle\CoreBundle\Dashboard\Position", "all"}, strict=true)
     *
     * @ORM\Column(type="string", length=20)
     */
    protected $position;

    /**
     * @var int
     *
     * @Assert\NotNull()
     *
     * @ORM\Column(type="integer")
     */
    protected $priority;

    public function __construct()
    {
        $this->position = Position::SIDEBAR;
        $this->priority = 0;
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

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

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}
