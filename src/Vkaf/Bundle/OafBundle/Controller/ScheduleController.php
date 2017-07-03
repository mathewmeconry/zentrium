<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use League\Period\Period;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;

/**
 * @Route("/oaf/schedules")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("/{schedule}/slots/next", name="oaf_schedule_slot_next")
     */
    public function slotNextAction(Schedule $schedule)
    {
        $offset = time() - $schedule->getBegin()->getTimestamp();
        $slot = max(0, min($schedule->getSlotCount(), ceil($offset / $schedule->getSlotDuration())));

        return $this->redirectToRoute('oaf_schedule_slot', ['schedule' => $schedule->getId(), 'slot' => $slot]);
    }

    /**
     * @Route("/{schedule}/slots/{slot}", name="oaf_schedule_slot")
     * @Template
     */
    public function slotAction(Schedule $schedule, $slot)
    {
        $slot = intval($slot);
        if ($slot < 0 || $slot > $schedule->getSlotCount()) {
            throw $this->createNotFoundException();
        }

        $slotDate = DateTimeImmutable::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $slot * $schedule->getSlotDuration());

        $shifts = $this->get('vkaf_oaf.repository.shift')->findAdjacent($schedule, $slotDate);
        $groupedShifts = [];
        foreach ($shifts as $shift) {
            if ($shift->getTask()->isInformative()) {
                continue;
            }
            $userId = $shift->getUser()->getId();
            if (!isset($groupedShifts[$userId])) {
                $groupedShifts[$userId] = [
                    'user' => $shift->getUser(),
                    'ending' => [],
                    'beginning' => [],
                ];
            }
            if ($shift->getTo() == $slotDate) {
                $groupedShifts[$userId]['ending'][] = $shift;
            } else {
                $groupedShifts[$userId]['beginning'][] = $shift;
            }
        }

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'groupedShifts' => $groupedShifts,
        ];
    }

    /**
     * @Route("/{schedule}/slots/{slot}/print", name="oaf_schedule_slot_print")
     * @Template
     */
    public function slotPrintAction(Schedule $schedule, $slot)
    {
        $slot = intval($slot);
        if ($slot < 0 || $slot > $schedule->getSlotCount()) {
            throw $this->createNotFoundException();
        }

        $slotDate = DateTimeImmutable::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $slot * $schedule->getSlotDuration());

        $shifts = $this->get('vkaf_oaf.repository.shift')->findAdjacent($schedule, $slotDate);
        $tasks = [];
        $users = [];
        foreach ($shifts as $shift) {
            if ($shift->getTask()->isInformative()) {
                continue;
            }

            $taskId = $shift->getTask()->getId();
            if (!isset($tasks[$taskId])) {
                $tasks[$taskId] = [
                    'task' => $shift->getTask(),
                    'start' => [],
                    'end' => [],
                ];
            }

            $userId = $shift->getUser()->getId();
            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'start' => [],
                    'end' => [],
                ];
            }

            if ($shift->getTo() == $slotDate) {
                $tasks[$taskId]['end'][] = $shift->getUser();
                $users[$userId]['end'][] = $shift->getTask();
            } else {
                $tasks[$taskId]['start'][] = $shift->getUser();
                $users[$userId]['start'][] = $shift->getTask();
            }
        }

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'tasks' => $tasks,
            'users' => $users,
        ];
    }

    /**
     * @Route("/user/{user}", name="oaf_schedule_user")
     * @Template
     */
    public function userAction(Request $request, User $user)
    {
        $schedules = $this->get('zentrium_schedule.manager.schedule')->findAll();
        $schedule = $this->getSchedule($request, $schedules);

        return [
            'user' => $user,
            'schedules' => $schedules,
            'schedule' => $schedule,
            'scheduleConfig' => [
                'begin' => $this->serializeDate($schedule->getBegin()),
                'duration' => $schedule->getPeriod()->getTimestampInterval(),
                'slotDuration' => $schedule->getSlotDuration(),
                'shifts' => $this->generateUrl('oaf_schedule_user_shifts', ['schedule' => $schedule->getId(), 'user' => $user->getId()]),
                'availabilities' => $this->generateUrl('oaf_schedule_user_availabilities', ['schedule' => $schedule->getId(), 'user' => $user->getId()]),
                'dayCount' => $this->countDays($schedule),
            ],
        ];
    }

    /**
     * @Route("/{schedule}/user/{user}/shifts.json", name="oaf_schedule_user_shifts")
     */
    public function userShiftsAction(Schedule $schedule, User $user)
    {
        $result = [];
        $shifts = $this->get('doctrine.orm.entity_manager')->getRepository(Shift::class)->findBy([
            'schedule' => $schedule,
            'user' => $user,
        ]);

        foreach ($shifts as $shift) {
            $result[] = [
                'id' => $shift->getId(),
                'title' => sprintf('%s (%s)', $shift->getTask()->getName(), $shift->getTask()->getCode()),
                'start' => $this->serializeDate($shift->getFrom()),
                'end' => $this->serializeDate($shift->getTo()),
                'color' => $shift->getTask()->getColor(),
                'timesheet' => $this->generateUrl('timesheet_entry_new', ['shift' => $shift->getId()]),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/{schedule}/user/{user}/availabilities.json", name="oaf_schedule_user_availabilities")
     */
    public function userAvailabilitiesAction(Schedule $schedule, User $user)
    {
        $paddedSchedulePeriod = $this->padPeriod($schedule->getPeriod(), new DateInterval('P1D'));
        $unavailable = [$paddedSchedulePeriod];

        $scheduleUser = $this->get('zentrium_schedule.manager.user')->findOneByBase($user);
        $availablities = $this->get('zentrium_schedule.manager.availability')->findOverlappingByUser($paddedSchedulePeriod, $scheduleUser);
        foreach ($availablities as $availability) {
            $newUnavailable = [];
            foreach ($unavailable as $period) {
                if (!$period->overlaps($availability->getPeriod())) {
                    $newUnavailable[] = $period;
                    continue;
                }
                $diffs = $period->diff($availability->getPeriod());
                foreach ($diffs as $diff) {
                    if ($period->contains($diff)) {
                        $newUnavailable[] = $diff;
                    }
                }
            }
            $unavailable = $newUnavailable;
        }

        $result = [];
        foreach ($unavailable as $period) {
            $result[] = [
                'start' => $this->serializeDate($period->getStartDate()),
                'end' => $this->serializeDate($period->getEndDate()),
                'color' => '#ccc',
                'rendering' => 'background',
            ];
        }

        return new JsonResponse($result);
    }

    private function getSchedule(Request $request, $schedules)
    {
        $scheduleId = intval($request->query->get('schedule'));
        $publishedSchedule = null;
        foreach ($schedules as $schedule) {
            if ($schedule->getId() === $scheduleId) {
                return $schedule;
            }
            if ($schedule->isPublished()) {
                $publishedSchedule = $schedule;
            }
        }

        return $publishedSchedule ?: $schedules[0];
    }

    private function serializeDate(DateTimeInterface $date)
    {
        static $timezone;
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $date->setTimezone($timezone)->format(DateTime::ATOM);
    }

    private function countDays(Schedule $schedule)
    {
        $offset = (new DateTimeImmutable())->getOffset();
        $beginDay = floor(($schedule->getBegin()->getTimestamp() - $offset) / (60 * 60 * 24));
        $endDay = floor(($schedule->getEnd()->getTimestamp() - $offset) / (60 * 60 * 24));

        return ($endDay - $beginDay + 1);
    }

    private function padPeriod(Period $period, DateInterval $padding)
    {
        return new Period($period->getStartDate()->sub($padding), $period->getEndDate()->add($padding));
    }
}
