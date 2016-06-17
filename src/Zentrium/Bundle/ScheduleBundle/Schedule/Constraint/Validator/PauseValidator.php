<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use DateTime;
use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DateTimeHelper;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Message;
use Zentrium\Bundle\ScheduleBundle\Util\SlotUtil;

class PauseValidator implements ValidatorInterface
{
    private $dateTimeHelper;
    private $durationHelper;

    public function __construct(DateTimeHelper $dateTimeHelper, DurationHelper $durationHelper)
    {
        $this->dateTimeHelper = $dateTimeHelper;
        $this->durationHelper = $durationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint)
    {
        $workSlots = floor($constraint->getParameters()['limit'] / $schedule->getSlotDuration());
        $pauseSlots = ceil($constraint->getParameters()['pause'] / $schedule->getSlotDuration());
        $messages = [];

        list($workMatrix, $userMap) = $this->buildWorkMatrix($schedule, $workSlots);
        foreach ($workMatrix as $userId => $row) {
            $user = $userMap[$userId];
            $working = 0;
            $workStart = false;
            $consecutivePause = 0;
            for ($i = 0; $i < count($row); ++$i) {
                if ($row[$i]) {
                    $working++;
                    $consecutivePause = 0;
                    if ($workStart === false) {
                        $workStart = $i;
                    }
                } else {
                    $consecutivePause++;
                    if ($consecutivePause >= $pauseSlots) { // guaranteed to be true at the end
                        if ($working > $workSlots) {
                            $messages[] = $this->buildMessage($constraint, $schedule, $userMap[$userId], $workStart, $i - $pauseSlots + 1);
                        }
                        $working = 0;
                        $workStart = false;
                    }
                }
            }
        }

        return $messages;
    }

    private function buildWorkMatrix(Schedule $schedule, $pauseSlots)
    {
        $matrix = [];
        $userMap = [];

        foreach ($schedule->getShifts() as $shift) {
            $userId = $shift->getUser()->getId();
            if (!isset($matrix[$userId])) {
                $matrix[$userId] = array_fill(0, $schedule->getSlotCount() + $pauseSlots, false);
                $userMap[$userId] = $shift->getUser();
            }
            list($fromIndex, $toIndex) = SlotUtil::coverIndices($schedule->getBegin(), $schedule->getSlotDuration(), $shift->getPeriod());
            for ($i = $fromIndex; $i < $toIndex; ++$i) {
                $matrix[$userId][$i] = true;
            }
        }

        return [$matrix, $userMap];
    }

    private function buildMessage(ConstraintInterface $constraint, Schedule $schedule, User $user, $fromIndex, $toIndex)
    {
        $from = DateTime::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $fromIndex * $schedule->getSlotDuration());
        $to = DateTime::createFromFormat('U', $schedule->getBegin()->getTimestamp() + $toIndex * $schedule->getSlotDuration());

        return Message::warning($constraint, 'zentrium_schedule.constraint.pause.message', [
            '%user%' => $user->getName(),
            '%from%' => $this->dateTimeHelper->format($from, 'datetime_medium'),
            '%to%' => $this->dateTimeHelper->format($to, 'datetime_medium'),
            '%pause%' => $this->durationHelper->format($constraint->getParameters()['pause']),
        ], [$user], new Period($from, $to));
    }
}
