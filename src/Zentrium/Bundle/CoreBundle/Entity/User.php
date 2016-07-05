<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Zentrium\Bundle\CoreBundle\Entity\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
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
    protected $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=50)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\Choice(choices={"male", "female"})
     *
     * @ORM\Column(name="gender", type="string", length=8, nullable=true)
     */
    protected $gender;

    /**
     * @var DateTimeInterface
     *
     * @Assert\Type(type="DateTimeInterface")
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @var PhoneNumber
     *
     * @AssertPhoneNumber
     *
     * @ORM\Column(type="phone_number", nullable=true)
     */
    protected $mobilePhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $title;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Group")
     */
    protected $groups;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $present;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    protected $bednumber;

    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
        $this->present = false;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getName($sortable = false)
    {
        if ($sortable) {
            return sprintf('%s %s', $this->getLastName(), $this->getFirstName());
        } else {
            return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
        }
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function setPresent($present)
    {
        $this->present = $present;

        return $this;
    }

    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
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

    public function getPresent()
    {
        return $this->present;
    }

    public function getBednumber()
    {
        return $this->bednumber;
    }

    public function setBednumber($bednumber)
    {
        $this->bednumber = $bednumber;

        return $this;
    }
}
