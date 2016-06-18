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
use Zentrium\Bundle\ScheduleBundle\Entity\AbstractItem;
use Zentrium\Bundle\ScheduleBundle\Entity\AbstractPlan;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ModifyOperationType;
use Zentrium\Bundle\ScheduleBundle\Form\Type\RequirementSetType;
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
     * @Route("/new", name="schedule_requirement_set_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new RequirementSet());
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
                'notes' => $task->getNotes(),
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
        $comparables = $this->get('zentrium_schedule.manager.requirement_set')->findComparables($set);

        $operations = [
            'set' => $this->generateUrl('schedule_requirement_set_set', ['set' => $set->getId()]),
            'modify' => $this->generateUrl('schedule_requirement_set_modify', ['set' => $set->getId()]),
        ];

        return [
            'set' => $set,
            'comparables' => $comparables,
            'config' => [
                'begin' => $this->serializeDate($set->getBegin()),
                'duration' => $set->getPeriod()->getTimestampInterval(),
                'slotDuration' => $set->getSlotDuration(),
                'requirements' => $this->generateUrl('schedule_requirement_set_requirements', ['set' => $set->getId()]),
                'tasks' => $this->generateUrl('schedule_requirement_set_tasks'),
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

    /**
     * @Route("/{set}/copy", name="schedule_requirement_set_copy")
     * @Template
     */
    public function copyAction(Request $request, RequirementSet $set)
    {
        $copy = $set->copy();
        $copy->setName($copy->getName().$this->get('translator')->trans('zentrium_schedule.requirement_set.copy.name_appendix'));

        return $this->handleEdit($request, $copy);
    }

    /**
     * @Route("/{set}/edit", name="schedule_requirement_set_edit")
     * @Template
     */
    public function editAction(Request $request, RequirementSet $set)
    {
        return $this->handleEdit($request, $set);
    }

    /**
     * @Route("/{set}/compare/{subject}", name="schedule_requirement_set_compare")
     * @Template
     */
    public function compareAction(RequirementSet $set, RequirementSet $subject)
    {
        return $this->handleDiffView($set, $subject, $this->generateUrl('schedule_requirement_set_compare_data', ['set' => $set->getId(), 'subject' => $subject->getId()]));
    }

    /**
     * @Route("/{set}/compare/{subject}/diff.json", name="schedule_requirement_set_compare_data")
     * @Template
     */
    public function compareDataAction(Request $request, RequirementSet $set, RequirementSet $subject)
    {
        return $this->handleDiffData($request, $set, $subject->getRequirements(), null, function ($a, $b) {
            $diff = $b - $a;

            return [
                'title' => sprintf('%s (%d → %d)', ($diff == 0 ? '=' : ($diff > 0 ? '+'.$diff : $diff)), $a, $b),
                'color' => ($diff == 0 ? '#009900' : ($diff > 0 ? '#ffaa00' : '#ff2222')),
            ];
        });
    }

    /**
     * @Route("/{set}/check/{schedule}", name="schedule_requirement_set_compare_schedule")
     * @Template
     */
    public function compareScheduleAction(RequirementSet $set, Schedule $schedule)
    {
        return $this->handleDiffView($set, $schedule, $this->generateUrl('schedule_requirement_set_compare_schedule_data', ['set' => $set->getId(), 'schedule' => $schedule->getId()]));
    }

    /**
     * @Route("/{set}/check/{schedule}/diff.json", name="schedule_requirement_set_compare_schedule_data")
     * @Template
     */
    public function compareScheduleDataAction(Request $request, RequirementSet $set, Schedule $schedule)
    {
        return $this->handleDiffData($request, $set, $schedule->getShifts(), 1, function ($a, $b) {
            $diff = $b - $a;

            return [
                'title' => sprintf('%s (%d/%d)', ($diff == 0 ? '✔' : (string) $diff), $a, $b),
                'color' => ($diff == 0 ? '#009900' : ($diff > 0 ? '#ff2222' : '#ffaa00')),
            ];
        });
    }

    private function handleDiffView(RequirementSet $set, AbstractPlan $subject, $dataUrl)
    {
        if (!$set->getPeriod()->contains($subject->getPeriod())) {
            $this->addFlash('warning', 'zentrium_schedule.requirement_set.compare.warn_boundaries', [
                '%a%' => $set->getName(),
                '%b%' => $subject->getName(),
            ]);
        }

        if ($subject->getSlotDuration() % $set->getSlotDuration() != 0 || !$set->isAligned($subject->getBegin())) {
            $this->addFlash('warning', 'zentrium_schedule.requirement_set.compare.warn_alignment', [
                '%a%' => $set->getName(),
                '%b%' => $subject->getName(),
            ]);
        }

        return [
            'set' => $set,
            'subject' => $subject,
            'config' => [
                'begin' => $this->serializeDate($set->getBegin()),
                'duration' => $set->getPeriod()->getTimestampInterval(),
                'slotDuration' => $set->getSlotDuration(),
                'requirements' => $dataUrl,
                'tasks' => $this->generateUrl('schedule_requirement_set_tasks'),
            ],
        ];
    }

    /**
     * @param RequirementSet $set
     * @param AbstractItem[] $subjectCollection
     * @param int|null       $subjectCount
     * @param callable       $formatter
     */
    private function handleDiffData(Request $request, RequirementSet $set, $subjectCollection, $subjectCount, $formatter)
    {
        $tz = new \DateTimeZone(date_default_timezone_get());
        $begin = $set->getBegin()->setTimezone($tz)->getTimestamp();
        $slotDuration = $set->getSlotDuration();
        $slotCount = $set->getSlotCount();

        // build row prototype
        $rowPrototype = [];
        $it = \DateTimeImmutable::createFromFormat('U', $set->getBegin()->getTimestamp(), $set->getBegin()->getTimezone());
        $it = $it->setTimezone($tz);
        $itDiff = new \DateInterval('PT'.$slotDuration.'S');
        for ($i = 0;$i < $slotCount;$i++) {
            $nextIt = $it->add($itDiff);
            $rowPrototype[] = [
                'set' => 0,
                'subject' => 0,
                'start' => $this->serializeDate($it),
                'end' => $this->serializeDate($nextIt),
            ];
            $it = $nextIt;
        }

        // fill matrix
        $fillMatrix = function ($matrix, $rowPrototype, $key, $collection, $count) use ($tz, $begin, $slotCount, $slotDuration) {
            foreach ($collection as $item) {
                $taskId = $item->getTask()->getId();
                if (!isset($matrix[$taskId])) {
                    $matrix[$taskId] = $rowPrototype;
                }
                $firstSlot = floor(($item->getFrom()->setTimezone($tz)->getTimestamp() - $begin) / $slotDuration);
                $lastSlot = ceil(($item->getTo()->setTimezone($tz)->getTimestamp() - $begin) / $slotDuration);
                for ($i = max($firstSlot, 0);$i < min($lastSlot, $slotCount); $i++) {
                    $matrix[$taskId][$i][$key] += ($count !== null ? $count : $item->getCount());
                }
            }

            return $matrix;
        };
        $matrix = [];
        $matrix = $fillMatrix($matrix, $rowPrototype, 'set', $set->getRequirements(), null);
        $matrix = $fillMatrix($matrix, $rowPrototype, 'subject', $subjectCollection, $subjectCount);

        // merge cells
        $result = [];
        foreach ($matrix as $taskId => $cells) {
            $last = ['set' => 0, 'subject' => 0];
            $pending = null;
            foreach ($cells as $cell) {
                if ($last['set'] == $cell['set'] && $last['subject'] == $cell['subject']) {
                    if ($pending) {
                        $pending['end'] = $cell['end'];
                    }
                } else {
                    if ($pending) {
                        $result[] = $pending;
                        $pending = null;
                    }
                    if ($cell['set'] != 0 || $cell['subject'] != 0) {
                        $diff = $cell['set'] - $cell['subject'];
                        $pending = array_merge($cell, [
                            'id' => (count($result) + 1),
                            'resourceId' => $taskId,
                        ], $formatter($cell['set'], $cell['subject']));
                    }
                }
                $last = $cell;
            }
            if ($pending) {
                $result[] = $pending;
            }
        }

        return new JsonResponse($result);
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

    private function handleEdit(Request $request, RequirementSet $set)
    {
        $form = $this->createForm(RequirementSetType::class, $set, [
            'with_period' => $set->getRequirements()->isEmpty(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.requirement_set');
            $manager->save($set);

            $this->addFlash('success', 'zentrium_schedule.requirement_set.form.saved');

            return $this->redirectToRoute('schedule_requirement_set_view', ['set' => $set->getId()]);
        }

        return [
            'set' => $set,
            'form' => $form->createView(),
        ];
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
