<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;

/**
 * @NamePrefix("api_schedule_requirements")
 */
class RequirementSetApiController extends FOSRestController
{
    /**
     * @Get("/api/schedules/requirements/sets")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $sets = $this->get('zentrium_schedule.manager.requirement_set')->findAll();

        return $sets;
    }

    /**
     * @Get("/api/schedules/requirements/sets/{set}")
     * @View(serializerGroups={"Default", "details"})
     */
    public function getAction(RequirementSet $set)
    {
        return $set;
    }
}
