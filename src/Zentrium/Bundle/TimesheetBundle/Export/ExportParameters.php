<?php

namespace Zentrium\Bundle\TimesheetBundle\Export;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Zentrium\Bundle\CoreBundle\Entity\Group;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @Assert\Expression("this.getGroupFilter() === null || this.getUserFilter() === null", message="This is not a valid combination of filters.")
 */
class ExportParameters
{
    /**
     * @Assert\NotNull
     * @Assert\Choice({"csv", "report"}, strict=true)
     */
    protected $format;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     */
    protected $from;

    /**
     * @var DateTime
     *
     * @Assert\NotNull
     * @Assert\Expression("value === null || this.getFrom() <= value", message="This value is not a valid time.")
     */
    protected $to;

    /**
     * @var User|null
     */
    protected $userFilter;

    /**
     * @var Group|null
     */
    protected $groupFilter;

    public function __construct()
    {
        $this->from = new DateTime();
        $this->to = new DateTime();
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    public function getUserFilter()
    {
        return $this->userFilter;
    }

    public function setUserFilter($userFilter)
    {
        $this->userFilter = $userFilter;

        return $this;
    }

    public function getGroupFilter()
    {
        return $this->groupFilter;
    }

    public function setGroupFilter($groupFilter)
    {
        $this->groupFilter = $groupFilter;

        return $this;
    }
}
