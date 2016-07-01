<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;

/**
 * @NamePrefix("api_schedule_tasks")
 */
class TaskApiController extends FOSRestController
{
    /**
     * @Get("/api/schedules/tasks")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $tasks = $this->get('zentrium_schedule.manager.task')->findAll();

        return $tasks;
    }

    /**
     * @Get("/api/schedules/tasks/{task}")
     * @View(serializerGroups={"Default", "details"})
     */
    public function getAction(Task $task)
    {
        return $task;
    }
}
