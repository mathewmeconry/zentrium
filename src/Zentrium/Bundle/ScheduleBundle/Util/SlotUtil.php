<?php

namespace Zentrium\Bundle\ScheduleBundle\Util;

use DateTimeImmutable;
use DateTimeInterface;
use League\Period\Period;

class SlotUtil
{
    public static function cover(DateTimeInterface $base, $slotDuration, Period $period)
    {
        return new Period(
            self::before($base, $slotDuration, $period->getStartDate()),
            self::after($base, $slotDuration, $period->getEndDate())
        );
    }

    public static function before(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $offset = $time->getTimestamp() - $base->getTimestamp();
        $offset = $slotDuration * floor($offset / $slotDuration);

        return DateTimeImmutable::createFromFormat('U', $base->getTimestamp() + $offset);
    }

    public static function after(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $offset = $time->getTimestamp() - $base->getTimestamp();
        $offset = $slotDuration * ceil($offset / $slotDuration);

        return DateTimeImmutable::createFromFormat('U', $base->getTimestamp() + $offset);
    }
}
