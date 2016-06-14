<?php

namespace Zentrium\Bundle\TimesheetBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

class EntryEvent extends Event
{
    private $entry;
    private $request;

    public function __construct(Entry $entry, Request $request)
    {
        $this->entry = $entry;
        $this->request = $request;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
