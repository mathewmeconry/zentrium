<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ModifyOperationType;
use Zentrium\Bundle\ScheduleBundle\Form\Type\SetOperationType;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\ModifyOperation;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\SetOperation;

/**
 * @Route("/schedules/requirements/sets")
 */
class RequirementSetController extends Controller
{
    use ControllerTrait;

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
     * @Route("/tasks.json", name="schedule_requirement_set_tasks")
     */
    public function tasksAction(Request $request)
    {
        $manager = $this->get('zentrium_schedule.manager.requirement_set');
        $setId = intval($request->query->get('set'));
        $set = $manager->find($setId);
        if ($setId != 0 && $set === null) {
            throw $this->createNotFoundException();
        }

        $tasks = $set ? $manager->getTasks($set) : $this->get('zentrium_schedule.manager.task')->findAll();
        $result = [];
        foreach ($tasks as $task) {
            $result[] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'code' => $task->getCode(),
                'eventColor' => $task->getColor(),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{set}", name="schedule_requirement_set_view")
     * @Template
     */
    public function viewAction(RequirementSet $set)
    {
        $router = $this->get('router');

        $operations = [
            'set' => $router->generate('schedule_requirement_set_set', ['set' => $set->getId()]),
            'modify' => $router->generate('schedule_requirement_set_modify', ['set' => $set->getId()]),
        ];

        return [
            'set' => $set,
            'config' => [
                'begin' => $this->serializeDate($set->getBegin()),
                'duration' => $set->getPeriod()->getTimestampInterval(),
                'slotDuration' => $set->getSlotDuration(),
                'requirements' => $router->generate('schedule_requirement_set_requirements', ['set' => $set->getId()]),
                'tasks' => $router->generate('schedule_requirement_set_tasks'),
                'operations' => $operations,
            ],
        ];
    }

    /**
     * @Route("/{set}/requirements.json", name="schedule_requirement_set_requirements")
     */
    public function viewRequirementsAction(RequirementSet $set)
    {
        $requirements = [];
        foreach ($set->getRequirements() as $requirement) {
            $requirements[] = $this->serializeRequirement($requirement);
        }

        return new JsonResponse($requirements);
    }

    /**
     * @Route("/{set}/modify", name="schedule_requirement_set_modify", options={"protect": true})
     * @Method("POST")
     */
    public function modifyAction(Request $request, RequirementSet $set)
    {
        $form = $this->createForm(ModifyOperationType::class, new ModifyOperation());

        return $this->handleOperation($request, $set, $form);
    }

    /**
     * @Route("/{set}/set", name="schedule_requirement_set_set", options={"protect": true})
     * @Method("POST")
     */
    public function setAction(Request $request, RequirementSet $set)
    {
        $form = $this->createForm(SetOperationType::class, new SetOperation());

        return $this->handleOperation($request, $set, $form);
    }

    private function handleOperation(Request $request, RequirementSet $set, FormInterface $form)
    {
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return new JsonResponse(['success' => false], 400);
        }

        try {
            $operation = $form->getData();
            $manager = $this->get('zentrium_schedule.manager.requirement_set');
            $manager->apply($set, $operation);
        } catch (OperationException $e) {
            return new JsonResponse(['success' => false], 400);
        }

        $requirements = [];
        foreach ($set->getRequirements() as $requirement) {
            if ($requirement->getTask()->getId() === $operation->getTask()->getId()) {
                $requirements[] = $this->serializeRequirement($requirement);
            }
        }

        return new JsonResponse([
            'success' => true,
            'requirements' => $requirements,
            'updated' => $set->getUpdated()->getTimestamp(),
        ]);
    }

    private function serializeRequirement(Requirement $requirement)
    {
        return [
            'id' => $requirement->getId(),
            'resourceId' => $requirement->getTask()->getId(),
            'title' => (string) $requirement->getCount(),
            'start' => $this->serializeDate($requirement->getFrom()),
            'end' => $this->serializeDate($requirement->getTo()),
        ];
    }

    private function serializeDate(DateTimeInterface $date)
    {
        static $timezone;
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $date->setTimezone($timezone)->format(DateTime::ATOM);
    }
}
