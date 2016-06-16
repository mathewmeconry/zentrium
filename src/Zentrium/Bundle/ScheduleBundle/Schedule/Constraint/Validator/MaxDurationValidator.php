<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DateTimeHelper;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Message;

class MaxDurationValidator implements ValidatorInterface
{
    private $durationHelper;
    private $dateTimeHelper;

    public function __construct(DurationHelper $durationHelper, DateTimeHelper $dateTimeHelper)
    {
        $this->durationHelper = $durationHelper;
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint)
    {
        $messages = [];

        $users = [];
        $periods = [];
        $periodElements = [];
        foreach ($schedule->getShifts() as $shift) {
            $userId = $shift->getUser()->getId();
            if (!isset($users[$userId])) {
                $users[$userId] = $shift->getUser();
            }
            if (isset($periods[$userId])) {
                if ($shift->getPeriod()->getStartDate() > $periods[$userId]->getEndDate()) {
                    $this->validatePeriod($constraint, $shift->getUser(), $periods[$userId], $periodElements[$userId], $messages);
                    $periods[$userId] = $shift->getPeriod();
                    $periodElements[$userId] = [$shift];
                } else {
                    $periods[$userId] = $periods[$userId]->merge($shift->getPeriod());
                    $periodElements[$userId][] = $shift;
                }
            } else {
                $periods[$userId] = $shift->getPeriod();
                $periodElements[$userId] = [$shift];
            }
        }
        foreach ($periods as $userId => $period) {
            $this->validatePeriod($constraint, $users[$userId], $period, $periodElements[$userId], $messages);
        }

        return $messages;
    }

    private function validatePeriod(ConstraintInterface $constraint, User $user, Period $period, $elements, &$messages)
    {
        $duration = $period->getTimestampInterval();
        $maxDuration = $constraint->getParameters()['max'];
        if ($duration > $maxDuration) {
            $messages[] = Message::warning($constraint, 'zentrium_schedule.constraint.max_duration.message', [
                '%user%' => $user->getName(),
                '%duration%' => $this->durationHelper->format($duration),
                '%max_duration%' => $this->durationHelper->format($maxDuration),
                '%begin%' => $this->dateTimeHelper->format($period->getStartDate(), 'datetime_medium'),
            ]);
        }
    }
}
