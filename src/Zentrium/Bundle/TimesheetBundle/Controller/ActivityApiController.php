<?php

namespace Zentrium\Bundle\TimesheetBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @NamePrefix("api_timesheet_activities")
 */
class ActivityApiController extends FOSRestController
{
    /**
     * @Get("/api/timesheet/activities")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $activities = $this->get('zentrium_timesheet.manager.activity')->findAll();

        return $activities;
    }
}
