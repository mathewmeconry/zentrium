<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Util;

use DateTime;
use PHPUnit\Framework\TestCase;
use Zentrium\Bundle\ScheduleBundle\Util\SlotUtil;

class SlotUtilTest extends TestCase
{
    public function testBeforeAlignedPast()
    {
        $base = new DateTime('2015-02-01 04:00:00');
        $time = new DateTime('2015-02-01 02:20:00');
        $expected = clone $time;

        $this->assertEquals($expected, SlotUtil::before($base, 60 * 20, $time));
    }

    public function testBeforeAlignedFuture()
    {
        $base = new DateTime('2015-02-01 04:00:00');
        $time = new DateTime('2015-02-01 06:40:00');
        $expected = clone $time;

        $this->assertEquals($expected, SlotUtil::before($base, 60 * 20, $time));
    }

    public function testBeforeUnalignedPast()
    {
        $base = new DateTime('2015-02-01 04:00:00');
        $time = new DateTime('2015-02-01 02:35:00');
        $expected = new DateTime('2015-02-01 02:20:00');

        $this->assertEquals($expected, SlotUtil::before($base, 60 * 20, $time));
    }

    public function testBeforeUnalignedFuture()
    {
        $base = new DateTime('2015-02-01 04:00:00');
        $time = new DateTime('2015-02-01 06:35:00');
        $expected = new DateTime('2015-02-01 06:20:00');

        $this->assertEquals($expected, SlotUtil::before($base, 60 * 20, $time));
    }

    public function testBeforeBase()
    {
        $time = new DateTime('2015-02-01 04:00:00');

        $this->assertEquals($time, SlotUtil::before($time, 60 * 20, $time));
    }

    /**
     * @dataProvider provideBeforeIndexSamples
     */
    public function testBeforeIndex($base, $slotDuration, $date, $expectedIndex)
    {
        $this->assertSame($expectedIndex, SlotUtil::beforeIndex(new DateTime($base), $slotDuration, new DateTime($date)));
    }

    public function provideBeforeIndexSamples()
    {
        return [
            ['2015-02-01 04:00:00', 900, '2015-02-01 04:14:59', 0],
            ['2015-02-01 04:00:00', 900, '2015-02-01 04:15:00', 1],
            ['2015-02-01 04:00:00', 3600, '2015-02-02 03:59:00', 23],
            ['2015-02-01 04:00:00', 900, '2015-02-01 02:15:00', -7],
            ['2015-02-01 04:00:00', 900, '2015-02-01 02:29:00', -7],
        ];
    }

    /**
     * @dataProvider provideAfterIndexSamples
     */
    public function testAfterIndex($base, $slotDuration, $date, $expectedIndex)
    {
        $this->assertSame($expectedIndex, SlotUtil::afterIndex(new DateTime($base), $slotDuration, new DateTime($date)));
    }

    public function provideAfterIndexSamples()
    {
        return [
            ['2015-02-01 04:00:00', 900, '2015-02-01 04:14:59', 1],
            ['2015-02-01 04:00:00', 900, '2015-02-01 04:15:00', 1],
            ['2015-02-01 04:00:00', 3600, '2015-02-02 04:01:00', 25],
            ['2015-02-01 04:00:00', 900, '2015-02-01 02:15:00', -7],
            ['2015-02-01 04:00:00', 900, '2015-02-01 02:29:00', -6],
        ];
    }
}
