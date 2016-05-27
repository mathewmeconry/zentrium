<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;

/**
 * @Route("/schedules/requirements/sets")
 */
class RequirementSetController extends Controller
{
    /**
     * @Route("/", name="schedule_requirements")
     * @Template
     */
    public function indexAction()
    {
        $sets = $this->get('zentrium_schedule.manager.requirement_set')->findAll();

        return [
            'sets' => $sets,
        ];
    }

    /**
     * @Route("/{set}", name="schedule_requirement_set_view")
     * @Template
     */
    public function viewAction(RequirementSet $set)
    {
        $router = $this->get('router');

        return [
            'set' => $set,
            'config' => [
                'begin' => $set->getBegin()->format('Y-m-d'),
                'duration' => $set->getPeriod()->getTimestampInterval(),
                'resources' => $router->generate('schedule_requirement_set_tasks', ['set' => $set->getId()]),
                'events' => $router->generate('schedule_requirement_set_requirements', ['set' => $set->getId()]),
            ],
        ];
    }

    /**
     * @Route("/{set}/tasks.json", name="schedule_requirement_set_tasks")
     */
    public function viewTasksAction(RequirementSet $set)
    {
        $manager = $this->get('zentrium_schedule.manager.requirement_set');
        $tasks = [];
        foreach ($manager->getTasks($set) as $task) {
            $tasks[] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'code' => $task->getCode(),
                'eventColor' => $task->getColor(),
            ];
        }

        return new JsonResponse($tasks);
    }

    /**
     * @Route("/{set}/requirements.json", name="schedule_requirement_set_requirements")
     */
    public function viewRequirementsAction(RequirementSet $set)
    {
        $requirements = [];
        foreach ($set->getRequirements() as $requirement) {
            $requirements[] = [
                'id' => $requirement->getId(),
                'resourceId' => $requirement->getTask()->getId(),
                'start' => $requirement->getFrom()->format(\DateTime::ATOM),
                'end' => $requirement->getTo()->format(\DateTime::ATOM),
                'title' => (string) $requirement->getCount(),
            ];
        }

        return new JsonResponse($requirements);
    }
}
