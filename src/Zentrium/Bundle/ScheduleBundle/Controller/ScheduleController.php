<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/{schedule}/edit", name="schedule_edit")
     * @Template
     */
    public function editAction(Request $request, Schedule $schedule)
    {
        $form = $this->createForm(ScheduleType::class, $schedule);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.schedule');
            $manager->save($schedule);

            $this->addFlash('success', 'zentrium_schedule.schedule.form.saved');

            return $this->redirectToRoute('schedules');
        }

        return [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ];
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
     * @Route("/{schedule}/users.json", name="schedule_view_users")
     */
    public function viewUsersAction(Request $request, Schedule $schedule)
    {
        $users = $this->get('zentrium.repository.user')->findAll();
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
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
        $router = $this->get('router');

        return [
            'schedule' => $schedule,
            'config' => [
                'scheduleId' => $schedule->getId(),
                'layout' => $layout,
                'begin' => $this->serializeDate($schedule->getBegin()),
                'duration' => $schedule->getPeriod()->getTimestampInterval(),
                'slotDuration' => $schedule->getSlotDuration(),
                'shifts' => $router->generate('schedule_view_shifts', ['schedule' => $schedule->getId(), 'layout' => $layout]),
                'tasks' => $router->generate('schedule_view_tasks', ['schedule' => $schedule->getId()]),
                'users' => $router->generate('schedule_view_users', ['schedule' => $schedule->getId()]),
                'endpoint' => $router->generate('schedule_shift_new', ['layout' => $layout]),
            ],
        ];
    }

    /**
     * @Route("/shifts/new", name="schedule_shift_new", options={"protect": true})
     * @Method("POST")
     */
    public function newShiftAction(Request $request)
    {
        $shift = new Shift();

        return $this->handleShiftEdit($request, $shift, ShiftNewType::class);
    }

    /**
     * @Route("/shifts/{shift}", name="schedule_shift_edit", options={"protect": true})
     * @Method("PATCH")
     */
    public function editShiftAction(Request $request, Shift $shift)
    {
        return $this->handleShiftEdit($request, $shift, ShiftEditType::class);
    }

    /**
     * @Route("/shifts/{shift}", name="schedule_shift_delete", options={"protect": true})
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

    private function serializeShift(Shift $shift, $layout)
    {
        $router = $this->get('router');

        return [
            'id' => $shift->getId(),
            'resourceId' => ($layout === self::USER_LAYOUT ? $shift->getUser()->getId() : $shift->getTask()->getId()),
            'valueId' => ($layout === self::USER_LAYOUT ? $shift->getTask()->getId() : $shift->getUser()->getId()),
            'title' => ($layout === self::USER_LAYOUT ? $shift->getTask()->getName() : $shift->getUser()->getName()),
            'start' => $this->serializeDate($shift->getFrom()),
            'end' => $this->serializeDate($shift->getTo()),
            'color' => $shift->getTask()->getColor(),
            'endpoint' => $router->generate('schedule_shift_edit', ['shift' => $shift->getId(), 'layout' => $layout]),
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

    private function getLayout(Request $request)
    {
        return ($request->query->get('layout') === self::TASK_LAYOUT ? self::TASK_LAYOUT : self::USER_LAYOUT);
    }
}
