<?php

namespace Zentrium\Bundle\TimesheetBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class GetResponseEntryEvent extends EntryEvent
{
    private $response;

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}
