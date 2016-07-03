<?php

namespace Zentrium\Bundle\TimesheetBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

/**
 * @NamePrefix("api_timesheet_entries")
 */
class EntryApiController extends FOSRestController
{
    /**
     * @Get("/api/timesheet/entries")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $entries = $this->get('zentrium_timesheet.manager.entry')->findAll();

        return $entries;
    }

    /**
     * @Get("/api/timesheet/entries/{entry}")
     * @View(serializerGroups={"Default", "details"})
     */
    public function getAction(Entry $entry)
    {
        return $entry;
    }
}
