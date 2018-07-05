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
     * @Route("/{schedule}/slots/{slot}/changes", name="oaf_schedule_slot_changes")
     * @Template
     */
    public function slotChangesAction(Schedule $schedule, $slot)
    {
        list($slot, $slotDate, $tasks, $users) = $this->analyzeSlot($schedule, $slot);

        $tasks = array_filter($tasks, function ($row) use ($users) {
            foreach ($row['end'] as $user) {
                if (count($users[$user->getId()]['start']) > 0) {
                    return true;
                }
            }

            return false;
        });

        uasort($tasks, function ($a, $b) {
            return strcasecmp($a['task']->getName(), $b['task']->getName());
        });

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'tasks' => $tasks,
            'users' => $users,
        ];
    }

    /**
     * @Route("/{schedule}/slots/{slot}/print", name="oaf_schedule_slot_print")
     * @Template
     */
    public function slotPrintAction(Schedule $schedule, $slot)
    {
        list($slot, $slotDate, $tasks, $users) = $this->analyzeSlot($schedule, $slot);

        uasort($tasks, function ($a, $b) {
            return strcasecmp($a['task']->getName(), $b['task']->getName());
        });

        $ending = $changing = $starting = 0;
        foreach ($users as $user) {
            if (count($user['end']) && count($user['start'])) {
                $changing++;
            } elseif (count($user['end'])) {
                $ending++;
            } else {
                $starting++;
            }
        }

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'tasks' => $tasks,
            'users' => $users,
            'ending' => $ending,
            'changing' => $changing,
            'starting' => $starting,
        ];
    }

    /**
     * @Route("/{schedule}/slots/{slot}/wakeup", name="oaf_schedule_slot_wakeup")
     * @Template
     */
    public function slotWakeupAction(Schedule $schedule, $slot)
    {
        list($slot, $slotDate, $tasks, $users) = $this->analyzeSlot($schedule, $slot);

        $users = array_filter($users, function ($row) {
            return count($row['start']) > 0;
        });

        uasort($users, function ($a, $b) {
            return strnatcasecmp($a['user']->getBednumber() ?? '', $b['user']->getBednumber() ?? '');
        });

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'users' => $users,
        ];
    }

    /**
     * @Route("/{schedule}/slots/{slot}/list", name="oaf_schedule_slot_list")
     * @Template
     */
    public function slotListAction(Schedule $schedule, $slot)
    {
        list($slot, $slotDate) = $this->parseSlot($schedule, $slot);

        $shifts = $this->get('vkaf_oaf.repository.shift')->findActive($schedule, $slotDate);
        $tasks = [];
        foreach ($shifts as $shift) {
            if ($shift->getTask()->isInformative()) {
                continue;
            }

            $taskId = $shift->getTask()->getId();
            if (!isset($tasks[$taskId])) {
                $tasks[$taskId] = [
                    'task' => $shift->getTask(),
                    'users' => [],
                ];
            }

            $tasks[$taskId]['users'][] = $shift->getUser();
        }

        uasort($tasks, function ($a, $b) {
            return strcasecmp($a['task']->getName(), $b['task']->getName());
        });

        return [
            'schedule' => $schedule,
            'slot' => $slot,
            'slotDate' => $slotDate,
            'tasks' => $tasks,
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
                'timesheet' => $this->generateUrl('timesheet_entry_new', [
                    'shift' => $shift->getId(),
                    'return_oaf_schedule' => $schedule->getId(),
                ]),
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

    /**
     * @Route("/{schedule}/statistics", name="oaf_schedule_statistics")
     */
    public function statisticsAction(Schedule $schedule)
    {
        return $this->redirectToRoute('oaf_schedule_statistics_slots', ['schedule' => $schedule->getId()]);
    }

    /**
     * @Route("/{schedule}/statistics/slots", name="oaf_schedule_statistics_slots")
     * @Template
     */
    public function statisticsSlotsAction(Request $request, Schedule $schedule)
    {
        $days = $this->countDays($schedule);
        $day = intval($request->query->get('day'));
        $day = max(0, min($days - 1, $day));
        $dayPeriod = Period::createFromDay($schedule->getBegin())->move(sprintf('%d day', $day));

        $base = $dayPeriod->getStartDate()->getTimestamp() - $schedule->getSlotDuration();
        $end = $dayPeriod->getEndDate()->getTimestamp() + $schedule->getSlotDuration();

        $labels = [];
        for ($time = $base; $time < $end; $time += $schedule->getSlotDuration()) {
            $labels[] = date('H:i', $time);
        }

        $maxIndex = count($labels);
        $beginning = array_fill(0, $maxIndex, []);
        $ending = array_fill(0, $maxIndex, []);
        foreach ($schedule->getShifts() as $shift) {
            if ($shift->getTask()->isInformative()) {
                continue;
            }
            $beginIndex = ($shift->getFrom()->getTimestamp() - $base) / $schedule->getSlotDuration();
            $endIndex = ($shift->getTo()->getTimestamp() - $base) / $schedule->getSlotDuration();
            if ($endIndex < 0 || $beginIndex >= $maxIndex) {
                continue;
            }
            $userId = $shift->getUser()->getId();
            $beginning[max(0, $beginIndex)][] = $userId;
            $ending[min($maxIndex - 1, $endIndex)][] = $userId;
        }

        $changing = array_fill(0, $maxIndex, 0);
        $staying = array_fill(0, $maxIndex, 0);
        $last = 0;
        for ($i = 0; $i < $maxIndex; $i++) {
            foreach ($beginning[$i] as $userId) {
                $pos = array_search($userId, $ending[$i], true);
                if ($pos !== false) {
                    unset($ending[$i][$pos]);
                    $changing[$i]++;
                }
            }
            $beginning[$i] = count($beginning[$i]) - $changing[$i];
            $ending[$i] = count($ending[$i]);
            $staying[$i] = $last - $ending[$i] - $changing[$i];
            $last = $last + $beginning[$i] - $ending[$i];
        }

        $data = array_map(function ($set) {
            return array_slice($set, 1, -1);
        }, [
            'labels' => $labels,
            'beginning' => $beginning,
            'changing' => $changing,
            'staying' => $staying,
            'ending' => $ending,
        ]);

        return [
            'schedule' => $schedule,
            'date' => $dayPeriod->getStartDate(),
            'day' => $day,
            'days' => $days,
            'data' => $data,
        ];
    }

    /**
     * @Route("/{schedule}/statistics/workload", name="oaf_schedule_statistics_workload")
     * @Template
     */
    public function statisticsWorkloadAction(Schedule $schedule)
    {
        $userManager = $this->get('zentrium_schedule.manager.user');
        $users = [];
        foreach ($userManager->findWithAvailabilities() as $user) {
            $available = 0;
            foreach ($user->getAvailabilities() as $availability) {
                $available += $availability->getPeriod()->getTimestampInterval();
            }
            $users[$user->getBase()->getId()] = [$user->getBase(), 0, 0, $available];
        }

        foreach ($schedule->getShifts() as $shift) {
            if ($shift->getTask()->isInformative()) {
                continue;
            }
            $userId = $shift->getUser()->getId();
            $begin = $shift->getPeriod()->getStartDate();
            $daytime = new Period($begin->setTime(6, 0, 0), $begin->setTime(22, 0, 0));
            while (!$daytime->isAfter($shift->getTo())) {
                if ($daytime->overlaps($shift->getPeriod())) {
                    $users[$userId][1] += $daytime->intersect($shift->getPeriod())->getTimestampInterval();
                }
                $daytime = $daytime->move('1 day');
            }
            $users[$userId][2] += $shift->getPeriod()->getTimestampInterval();
        }

        return [
            'schedule' => $schedule,
            'users' => $users,
        ];
    }

    private function analyzeSlot(Schedule $schedule, $slot)
    {
        list($slot, $slotDate) = $this->parseSlot($schedule, $slot);

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
                    'user' => $shift->getUser(),
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

        return [$slot, $slotDate, $tasks, $users];
    }

    private function parseSlot(Schedule $schedule, $slot)
    {
        $slot = intval($slot);
        if ($slot < 0 || $slot > $schedule->getSlotCount()) {
            throw $this->createNotFoundException();
        }

        $slotDate = DateTimeImmutable::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $slot * $schedule->getSlotDuration());

        return [$slot, $slotDate];
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
