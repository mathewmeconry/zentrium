<?php

namespace Zentrium\Bundle\ScheduleBundle\Util;

use SplPriorityQueue;

class PeriodQueue extends SplPriorityQueue
{
    public function compare($a, $b)
    {
        if ($a->getStartDate() == $b->getStartDate()) {
            return $b->compareDuration($a);
        }

        return $a->getStartDate() < $b->getStartDate() ? 1 : -1;
    }
}
