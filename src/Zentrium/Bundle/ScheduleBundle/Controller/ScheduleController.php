<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ScheduleType;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ShiftEditType;
use Zentrium\Bundle\ScheduleBundle\Form\Type\ShiftNewType;

/**
 * @Route("/schedules")
 */
class ScheduleController extends Controller
{
    const TASK_LAYOUT = 'task';
    const USER_LAYOUT = 'user';

    use ControllerTrait;

    /**
     * @Route("/", name="schedules")
     * @Template
     */
    public function indexAction()
    {
        $schedules = $this->get('zentrium_schedule.manager.schedule')->findAll();

        return [
            'schedules' => $schedules,
        ];
    }

    /**
     * @Route("/new", name="schedule_new")
     * @IsGranted("ROLE_SCHEDULER")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Schedule());
    }

    /**
     * @Route("/{schedule}/edit", name="schedule_edit")
     * @IsGranted("ROLE_SCHEDULER")
     * @Template
     */
    public function editAction(Request $request, Schedule $schedule)
    {
        return $this->handleEdit($request, $schedule);
    }

    /**
     * @Route("/{schedule}/copy", name="schedule_copy")
     * @IsGranted("ROLE_SCHEDULER")
     * @Template
     */
    public function copyAction(Request $request, Schedule $schedule)
    {
        $copy = $schedule->copy();
        $copy->setName($copy->getName().$this->get('translator')->trans('zentrium_schedule.schedule.copy.name_appendix'));

        return $this->handleEdit($request, $copy);
    }

    /**
     * @Route("/{schedule}/validate", name="schedule_validate")
     * @Template
     */
    public function validateAction(Request $request, Schedule $schedule)
    {
        $constraints = $this->get('zentrium_schedule.manager.constraint')->findAll();

        if ($request->query->has('constraints')) {
            $activeConstraints = array_map('intval', explode(' ', $request->query->get('constraints')));
        } else {
            $activeConstraints = null;
        }

        $defaultConstraints = array_map(function ($constraint) {
            return $constraint->getId();
        }, $schedule->getDefaultConstraints()->toArray());

        return [
            'schedule' => $schedule,
            'constraints' => $constraints,
            'active' => $activeConstraints,
            'defaults' => $defaultConstraints,
        ];
    }

    /**
     * @Route("/{schedule}/validate/result.json", name="schedule_validate_result")
     * @ParamConverter("schedule", options={"repository_method": "findWithAssociations"})
     */
    public function validateResultAction(Request $request, Schedule $schedule)
    {
        $ids = array_map('intval', explode(' ', $request->query->get('constraints')));
        $constraints = $this->get('zentrium_schedule.manager.constraint')->loadMultiple($ids);

        $messages = $this->get('zentrium_schedule.schedule.constraint_checker')->check($schedule, $constraints);

        $translator = $this->get('translator');
        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'level' => $message->getLevel(),
                'message' => $translator->trans($message->getMessageKey(), $message->getMessageParameters()),
                'constraintName' => $message->getConstraint()->getName(),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}/validate/defaults.json", name="schedule_validate_defaults", options={"protect": true})
     * @IsGranted("ROLE_SCHEDULER")
     * @Method("PATCH")
     */
    public function validateDefaultsAction(Request $request, Schedule $schedule)
    {
        $ids = array_unique(array_map('intval', $request->request->get('defaults', [])));

        $delete = [];
        foreach ($schedule->getDefaultConstraints() as $key => $constraint) {
            $pos = array_search($constraint->getId(), $ids, true);
            if ($pos === false) {
                $delete[] = $key;
            } else {
                unset($ids[$pos]);
            }
        }

        foreach ($delete as $key) {
            $schedule->getDefaultConstraints()->remove($key);
        }

        $new = $this->get('zentrium_schedule.manager.constraint')->findMultiple($ids);
        foreach ($new as $constraint) {
            $schedule->getDefaultConstraints()->add($constraint);
        }

        $this->get('zentrium_schedule.manager.schedule')->save($schedule);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{schedule}/shifts.json", name="schedule_view_shifts")
     */
    public function viewShiftsAction(Request $request, Schedule $schedule)
    {
        $layout = $this->getLayout($request);
        $result = [];
        foreach ($schedule->getShifts() as $shift) {
            $result[] = $this->serializeShift($shift, $layout);
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}/availabilities.json", name="schedule_view_availabilities")
     */
    public function viewAvailabilitiesAction(Request $request, Schedule $schedule)
    {
        $availabilities = $this->get('zentrium_schedule.manager.availability')->findOverlapping($schedule->getPeriod());

        $result = [];
        $coveredUsers = [];
        foreach ($availabilities as $availability) {
            $userId = $availability->getUser()->getBase()->getId();
            $coveredUsers[$userId] = true;
            $result[] = [
                'id' => 'a'.$userId,
                'resourceId' => $userId,
                'start' => $this->serializeDate($availability->getFrom()),
                'end' => $this->serializeDate($availability->getTo()),
                'color' => '#cccccc',
                'rendering' => 'inverse-background',
            ];
        }

        $users = $this->get('zentrium.repository.user')->findAll();
        $scheduleBegin = $this->serializeDate($schedule->getBegin());
        $scheduleEnd = $this->serializeDate($schedule->getEnd());
        foreach ($users as $user) {
            $userId = $user->getId();
            if (isset($coveredUsers[$userId])) {
                continue;
            }
            $result[] = [
                'id' => 'a'.$userId,
                'resourceId' => $userId,
                'start' => $scheduleBegin,
                'end' => $scheduleEnd,
                'color' => '#cccccc',
                'rendering' => 'background',
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}/users.json", name="schedule_view_users")
     */
    public function viewUsersAction(Request $request, Schedule $schedule)
    {
        $users = $this->get('zentrium_schedule.manager.user')->findAll();

        $result = [];
        foreach ($users as $user) {
            $groups = array_map(function ($group) {
                return $group->getShortName();
            }, $user->getBase()->getGroups()->toArray());

            $skills = array_map(function ($skill) {
                return [
                    'id' => $skill->getId(),
                    'name' => $skill->getShortName(),
                ];
            }, $user->getSkills()->toArray());

            $result[] = [
                'id' => $user->getBase()->getId(),
                'name' => $user->getBase()->getName(true),
                'groups' => $groups,
                'skills' => $skills,
                'notes' => $user->getNotes(),
                'availability' => $this->generateUrl('schedule_user_availability', ['user' => $user->getBase()->getId()]),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}/tasks.json", name="schedule_view_tasks")
     */
    public function viewTasksAction()
    {
        $tasks = $this->get('zentrium_schedule.manager.task')->findAll();
        $result = [];
        foreach ($tasks as $task) {
            $result[] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'code' => $task->getCode(),
                'skill' => (($skill = $task->getSkill()) !== null ? $skill->getId() : null),
                'notes' => $task->getNotes(),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}", name="schedule_view")
     * @Template
     */
    public function viewAction(Request $request, Schedule $schedule)
    {
        $layout = $this->getLayout($request);

        $editable = ($this->isGranted('ROLE_SCHEDULER') && $layout === self::USER_LAYOUT);

        $comparableSets = $this->get('zentrium_schedule.manager.requirement_set')->findComparables($schedule);

        return [
            'schedule' => $schedule,
            'comparableSets' => $comparableSets,
            'timesheet' => $this->getParameter('zentrium_schedule.timesheet'),
            'config' => [
                'scheduleId' => $schedule->getId(),
                'name' => $schedule->getName(),
                'layout' => $layout,
                'begin' => $this->serializeDate($schedule->getBegin()),
                'duration' => $schedule->getPeriod()->getTimestampInterval(),
                'slotDuration' => $schedule->getSlotDuration(),
                'shifts' => $this->generateUrl('schedule_view_shifts', ['schedule' => $schedule->getId(), 'layout' => $layout]),
                'availabilities' => $this->generateUrl('schedule_view_availabilities', ['schedule' => $schedule->getId()]),
                'tasks' => $this->generateUrl('schedule_view_tasks', ['schedule' => $schedule->getId()]),
                'users' => $this->generateUrl('schedule_view_users', ['schedule' => $schedule->getId()]),
                'endpoint' => $editable ? $this->generateUrl('schedule_shift_new', ['layout' => $layout]) : null,
            ],
        ];
    }

    /**
     * @Route("/shifts/new", name="schedule_shift_new", options={"protect": true})
     * @IsGranted("ROLE_SCHEDULER")
     * @Method("POST")
     */
    public function newShiftAction(Request $request)
    {
        $shift = new Shift();

        return $this->handleShiftEdit($request, $shift, ShiftNewType::class);
    }

    /**
     * @Route("/shifts/{shift}", name="schedule_shift_edit", options={"protect": true})
     * @IsGranted("ROLE_SCHEDULER")
     * @Method("PATCH")
     */
    public function editShiftAction(Request $request, Shift $shift)
    {
        return $this->handleShiftEdit($request, $shift, ShiftEditType::class);
    }

    /**
     * @Route("/shifts/{shift}", name="schedule_shift_delete", options={"protect": true})
     * @IsGranted("ROLE_SCHEDULER")
     * @Method("DELETE")
     */
    public function deleteShiftAction(Request $request, Shift $shift)
    {
        $manager = $this->get('zentrium_schedule.manager.shift');
        $manager->delete($shift);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function handleShiftEdit(Request $request, Shift $shift, $formClass)
    {
        $form = $this->createForm($formClass, $shift);

        $form->submit($request->request->get($form->getName()), false);

        if (!$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $manager = $this->get('zentrium_schedule.manager.shift');
        $manager->save($shift);

        return new JsonResponse([
            'shift' => $this->serializeShift($shift, $this->getLayout($request)),
        ]);
    }

    private function handleEdit(Request $request, Schedule $schedule)
    {
        $form = $this->createForm(ScheduleType::class, $schedule, [
            'with_period' => $schedule->getShifts()->isEmpty(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.schedule');
            $manager->save($schedule);

            $this->addFlash('success', 'zentrium_schedule.schedule.form.saved');

            return $this->redirectToRoute('schedule_view', ['schedule' => $schedule->getId()]);
        }

        return [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ];
    }

    private function serializeShift(Shift $shift, $layout)
    {
        $result = [
            'id' => $shift->getId(),
            'resourceId' => ($layout === self::USER_LAYOUT ? $shift->getUser()->getId() : $shift->getTask()->getId()),
            'valueId' => ($layout === self::USER_LAYOUT ? $shift->getTask()->getId() : $shift->getUser()->getId()),
            'title' => ($layout === self::USER_LAYOUT ? $shift->getTask()->getName() : $shift->getUser()->getName()),
            'start' => $this->serializeDate($shift->getFrom()),
            'end' => $this->serializeDate($shift->getTo()),
            'color' => $shift->getTask()->getColor(),
            'endpoint' => $this->generateUrl('schedule_shift_edit', ['shift' => $shift->getId(), 'layout' => $layout]),
        ];

        if ($this->getParameter('zentrium_schedule.timesheet')) {
            $result['timesheet'] = $this->generateUrl('timesheet_entry_new', ['shift' => $shift->getId()]);
        }

        return $result;
    }

    private function serializeDate(DateTimeInterface $date)
    {
        static $timezone;
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $date->setTimezone($timezone)->format(DateTime::ATOM);
    }

    private function getLayout(Request $request)
    {
        return ($request->query->get('layout') === self::TASK_LAYOUT ? self::TASK_LAYOUT : self::USER_LAYOUT);
    }
}
