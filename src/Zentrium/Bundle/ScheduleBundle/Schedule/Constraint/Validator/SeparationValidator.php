<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Message;

class SeparationValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint)
    {
        $messages = [];

        $last = [];
        foreach ($schedule->getShifts() as $shift) {
            $userId = $shift->getUser()->getId();
            if (!isset($last[$userId])) {
                $last[$userId] = $shift->getTo();
                continue;
            }

            if ($shift->getFrom() < $last[$userId]) {
                $messages[] = Message::critical($constraint, 'zentrium_schedule.constraint.separation.message', [
                    '%user%' => $shift->getUser()->getName(),
                ], [$shift]);
            }

            if ($shift->getTo() > $last[$userId]) {
                $last[$userId] = $shift->getTo();
            }
        }

        return $messages;
    }
}
