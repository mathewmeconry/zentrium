<?php

namespace Vkaf\Bundle\OafBundle\Kiosk\Slide;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Entity\ShiftManager;

class ScheduleSlide implements SlideInterface
{
    private $shiftManager;
    private $userRepository;

    public function __construct(ShiftManager $shiftManager, UserRepository $userRepository)
    {
        $this->shiftManager = $shiftManager;
        $this->userRepository = $userRepository;
    }

    public function render($options, $next)
    {
        $period = Period::createFromDuration('- 2 hours', '12 hours');
        // align to interval

        $shifts = $this->shiftManager->findPublishedInPeriod($period);
        $serializedShifts = [];
        foreach ($shifts as $shift) {
            $serializedShifts[] = $this->serializeShift($shift);
        }

        $users = $this->userRepository->findAll();
        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = $this->serializeUser($user);
        }
        $partitionIndex = ceil(count($users) / 2);
        $serializedUserPartitions = [array_slice($serializedUsers, 0, $partitionIndex), array_slice($serializedUsers, $partitionIndex)];

        return [
            'begin' => $this->serializeDate($period->getStartDate()),
            'duration' => $period->getTimestampInterval(),
            'slotDuration' => 3600,
            'userPartitions' => $serializedUserPartitions,
            'shifts' => $serializedShifts,
            'next' => $next,
        ];
    }

    private function serializeUser(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(true),
        ];
    }

    private function serializeShift(Shift $shift)
    {
        return [
            'id' => $shift->getId(),
            'resourceId' => $shift->getUser()->getId(),
            'color' => $shift->getTask()->getColor(),
            'title' => sprintf('%s (%s)', $shift->getTask()->getName(), $shift->getTask()->getCode()),
            'start' => $this->serializeDate($shift->getFrom()),
            'end' => $this->serializeDate($shift->getTo()),
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
