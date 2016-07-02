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
use Vkaf\Bundle\OafBundle\Form\Type\UserScheduleType;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;

/**
 * @Route("/oaf/schedules")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("/user/dashboard", name="oaf_schedule_user_dashboard")
     * @Template
     */
    public function userDashboardAction(Request $request)
    {
        $form = $this->createForm(UserScheduleType::class);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('oaf_schedule_user', ['user' => $form->getData()['user']->getId()]);
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
