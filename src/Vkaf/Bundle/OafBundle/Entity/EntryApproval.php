<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vkaf\Bundle\OafBundle\Validator\Constraints as AssertOaf;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

/**
 * @ORM\Entity
 * @ORM\Table(name="oaf_entry_approval")
 */
class EntryApproval
{
    /**
     * @var Entry
     *
     * @Assert\Valid
     * @Assert\Expression("value.isApproved()")
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Zentrium\Bundle\TimesheetBundle\Entity\Entry")
     * @ORM\JoinColumn(name="id", onDelete="CASCADE")
     */
    private $entry;

    /**
     * @var array
     *
     * @AssertOaf\Signature
     *
     * @ORM\Column(type="json_array", nullable=false)
     */
    private $signature;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="Zentrium\Bundle\CoreBundle\Entity\User")
     */
    protected $attester;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    public function getAttester()
    {
        return $this->attester;
    }

    public function setAttester($attester)
    {
        $this->attester = $attester;

        return $this;
    }
}
