<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vkaf\Bundle\OafBundle\Validator\Constraints as AssertOaf;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="oaf_push_subscription")
 */
class PushSubscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Url(protocols={"https"})
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $endpoint;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @AssertOaf\Base64
     *
     * @ORM\Column(name="key_", type="string")
     */
    protected $key;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @AssertOaf\Base64
     *
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $refreshed;

    public function __construct()
    {
        $this->created = new DateTime();
        $this->refresh();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;

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

    public function getCreated()
    {
        return $this->created;
    }

    public function getRefreshed()
    {
        return $this->refreshed;
    }

    public function refresh()
    {
        $this->refreshed = new DateTime();

        return $this;
    }
}
