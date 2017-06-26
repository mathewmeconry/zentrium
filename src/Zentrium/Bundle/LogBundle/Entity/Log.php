<?php

namespace Zentrium\Bundle\LogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\TimestampableTrait;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Zentrium\Bundle\LogBundle\Entity\LogRepository")
 * @ORM\Table(name="logs")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    const STATUS_OPEN = 'open';
    const STATUS_ON_HOLD = 'onhold';
    const STATUS_DONE = 'done';
    const STATUS_INVALID = 'invalid';

    use TimestampableTrait;

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
     * @Assert\Length(max=100)
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $reported;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deadline;

    /**
     * @var string
     *
     * @Assert\Length(max=100)
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $source;

    /**
     * @var string
     *
     * @Assert\Length(max=100)
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $resolution;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Choice(callback="getStatuses")
     *
     * @ORM\Column(type="string", length=20)
     */
    protected $status;

    /**
     * @ORM\ManyToMany(targetEntity="Label")
     */
    protected $labels;

    /**
     * @var User
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $author;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="log", cascade="ALL")
     * @ORM\OrderBy({"created"="ASC"})
     */
    protected $comments;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="change", field={"title", "details", "reported", "deadline", "source", "location", "resolution"})
     * @ORM\Column(type="datetime")
     */
    protected $edited;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    public function getReported()
    {
        return $this->reported;
    }

    public function setReported($reported)
    {
        $this->reported = $reported;

        return $this;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    public function getResolution()
    {
        return $this->resolution;
    }

    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    public function getEdited()
    {
        return $this->edited;
    }

    public function setEdited($edited)
    {
        $this->edited = $edited;

        return $this;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_ON_HOLD,
            self::STATUS_DONE,
            self::STATUS_INVALID,
        ];
    }
}
