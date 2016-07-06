<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="oaf_resource_assignment")
 */
class ResourceAssignment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Vkaf\Bundle\OafBundle\Entity\Resource
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $resource;

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
     * @var User
     *
     * @Assert\NotNull
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $assignedBy;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     *
     * @ORM\Column(type="datetime")
     */
    protected $assignedAt;

    /**
     * @var User
     *
     * @Assert\NotNull(groups="returned")
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $returnedBy;

    /**
     * @var DateTime
     *
     * @Assert\NotNull(groups="returned")
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $returnedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
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

    public function getAssignedBy()
    {
        return $this->assignedBy;
    }

    public function setAssignedBy($assignedBy)
    {
        $this->assignedBy = $assignedBy;

        return $this;
    }

    public function getAssignedAt()
    {
        return $this->assignedAt;
    }

    public function setAssignedAt($assignedAt)
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    public function getReturnedBy()
    {
        return $this->returnedBy;
    }

    public function setReturnedBy($returnedBy)
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    public function getReturnedAt()
    {
        return $this->returnedAt;
    }

    public function setReturnedAt($returnedAt)
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }
}
