<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Util;

use League\Period\Period;
use Zentrium\Bundle\ScheduleBundle\Util\PeriodQueue;

class PeriodQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider compareUnequalValues
     * @group isolate
     */
    public function testCompareUnequal(Period $lessPeriod, Period $greaterPeriod)
    {
        $queue = new PeriodQueue();

        $this->assertSame(1, $queue->compare($lessPeriod, $greaterPeriod));
        $this->assertSame(-1, $queue->compare($greaterPeriod, $lessPeriod));
    }

    public function compareUnequalValues()
    {
        return [
            [Period::createFromDuration('2015-04-03', '1 hour'), Period::createFromDuration('2015-04-03', '1 day')],
            [Period::createFromDuration('2015-04-03', '1 hour'), Period::createFromDuration('2015-04-05', '1 hour')],
            [Period::createFromDuration('2015-04-03', '1 day'), Period::createFromDuration('2015-04-05', '1 hour')],
        ];
    }

    public function testCompareEqual()
    {
        $a = Period::createFromDuration('2015-04-03', '1 day');
        $b = Period::createFromDuration('2015-04-03', '1 day');
        $queue = new PeriodQueue();

        $this->assertSame(0, $queue->compare($a, $b));
    }
}
