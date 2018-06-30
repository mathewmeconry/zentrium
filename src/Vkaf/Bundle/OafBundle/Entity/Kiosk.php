<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Vkaf\Bundle\OafBundle\Entity\KioskRepository")
 * @ORM\Table(name="oaf_kiosk")
 * @UniqueEntity({"token"})
 */
class Kiosk implements UserInterface
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
    protected $label;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $token;

    /**
     * @ORM\OneToMany(targetEntity="KioskSlide", mappedBy="kiosk", cascade="all")
     * @ORM\OrderBy({"order"="ASC"})
     */
    protected $slides;

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getSlides()
    {
        return $this->slides;
    }

    public function setSlides($slides)
    {
        $this->slides = $slides;

        return $this;
    }

    public function getUsername()
    {
        return $this->getLabel();
    }

    public function getRoles()
    {
        return ['ROLE_KIOSK'];
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }
}
