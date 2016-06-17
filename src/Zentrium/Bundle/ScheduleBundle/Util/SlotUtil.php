<?php

namespace Zentrium\Bundle\ScheduleBundle\Util;

use DateTimeImmutable;
use DateTimeInterface;
use League\Period\Period;

class SlotUtil
{
    public static function coverIndices(DateTimeInterface $base, $slotDuration, Period $period)
    {
        return [
            self::beforeIndex($base, $slotDuration, $period->getStartDate()),
            self::afterIndex($base, $slotDuration, $period->getEndDate()),
        ];
    }

    public static function cover(DateTimeInterface $base, $slotDuration, Period $period)
    {
        return new Period(
            self::before($base, $slotDuration, $period->getStartDate()),
            self::after($base, $slotDuration, $period->getEndDate())
        );
    }

    public static function beforeIndex(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $offset = $time->getTimestamp() - $base->getTimestamp();

        return (int) floor($offset / $slotDuration);
    }

    public static function before(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $timestamp = $base->getTimestamp() + $slotDuration * self::beforeIndex($base, $slotDuration, $time);

        return DateTimeImmutable::createFromFormat('U', $timestamp);
    }

    public static function afterIndex(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $offset = $time->getTimestamp() - $base->getTimestamp();

        return (int) ceil($offset / $slotDuration);
    }

    public static function after(DateTimeInterface $base, $slotDuration, DateTimeInterface $time)
    {
        $timestamp = $base->getTimestamp() + $slotDuration * self::afterIndex($base, $slotDuration, $time);

        return DateTimeImmutable::createFromFormat('U', $timestamp);
    }
}
