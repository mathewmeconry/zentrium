<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use DateTime;
use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DateTimeHelper;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Message;
use Zentrium\Bundle\ScheduleBundle\Util\SlotUtil;

class SleepValidator implements ValidatorInterface
{
    private $dateTimeHelper;

    public function __construct(DateTimeHelper $dateTimeHelper)
    {
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint)
    {
        $sleepSlots = ceil($constraint->getParameters()['minimum'] / $schedule->getSlotDuration());
        $daySlots = ceil(24 * 3600 / $schedule->getSlotDuration());
        $messages = [];

        list($sleepMatrix, $userMap) = $this->buildSleepMatrix($schedule, $sleepSlots);
        foreach ($sleepMatrix as $userId => $row) {
            $user = $userMap[$userId];
            $queueSize = 0;
            $sufficient = 0;
            $violated = false;
            for ($i = count($row) - 1; $i >= 0; --$i) {
                while ($queueSize >= $daySlots) {
                    if ($row[$i + $queueSize] >= $sleepSlots) {
                        $sufficient--;
                    }
                    $queueSize--;
                }

                if ($row[$i] > 0) { // sleeping
                    if ($violated) {
                        $messages[] = $this->buildMessage($constraint, $schedule, $user, $i + 1);
                    }
                    $violated = false;
                    if ($row[$i] >= $sleepSlots) {
                        $sufficient++;
                    }
                } elseif ($sufficient == 0) { // not enough sleep afterwards
                    $violated = true;
                }

                $queueSize++;
            }
            if ($violated) {
                $messages[] = $this->buildMessage($constraint, $schedule, $user, 0);
            }
        }

        return $messages;
    }

    /**
     * Builds a matrix where a cell indicates the number of consecutive slots
     * during which the user is sleeping.
     *
     * @param Schedule $schedule
     * @param int      $sleepSlots
     *
     * @return array A tuple consisting of the matrix and a map of all users
     */
    private function buildSleepMatrix(Schedule $schedule, $sleepSlots)
    {
        $matrix = [];
        $userMap = [];

        foreach ($schedule->getShifts() as $shift) {
            $userId = $shift->getUser()->getId();
            if (!isset($matrix[$userId])) {
                $matrix[$userId] = [];
                $userMap[$userId] = $shift->getUser();
            }
            list($fromIndex, $toIndex) = SlotUtil::coverIndices($schedule->getBegin(), $schedule->getSlotDuration(), $shift->getPeriod());
            for ($i = count($matrix[$userId]); $i < $fromIndex; ++$i) {
                $matrix[$userId][$i] = ($i > 0 ? $matrix[$userId][$i - 1] + 1 : 1);
            }
            for ($i = $fromIndex; $i < $toIndex; ++$i) {
                // assert(!isset($matrix[$userId][$i]) || $matrix[$userId][$i] === 0);
                $matrix[$userId][$i] = 0;
            }
        }

        $rowCount = $schedule->getSlotCount() + $sleepSlots;
        foreach ($matrix as &$row) {
            for ($i = count($row); $i < $rowCount; ++$i) {
                $row[$i] = ($i > 0 ? $row[$i - 1] + 1 : 1);
            }
        }
        unset($row);

        return [$matrix, $userMap];
    }

    private function buildMessage(ConstraintInterface $constraint, Schedule $schedule, User $user, $index)
    {
        $date = DateTime::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $index * $schedule->getSlotDuration());

        return Message::warning($constraint, 'zentrium_schedule.constraint.sleep.message', [
            '%user%' => $user->getName(),
            '%date%' => $this->dateTimeHelper->format($date, 'datetime_medium'),
        ], [], Period::createFromDuration($date, '1 day'));
    }
}
