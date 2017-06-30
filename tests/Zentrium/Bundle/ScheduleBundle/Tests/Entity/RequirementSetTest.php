<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Entity;

use DateTime;
use PHPUnit\Framework\TestCase;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;

class RequirementSetTest extends TestCase
{
    /**
     * @dataProvider isAlignedValues
     */
    public function testIsAligned(DateTime $value, $expected)
    {
        $set = new RequirementSet();
        $set->setBegin(new DateTime('2015-05-01 06:00:30'));
        $set->setSlotDuration(60 * 20);

        $actual = $set->isAligned($value);

        $this->assertSame($expected, $actual);
    }

    public function isAlignedValues()
    {
        return [
            [new DateTime('2015-03-01 04:40:30'), true],
            [new DateTime('2015-05-01 06:00:30'), true],
            [new DateTime('2015-05-05 06:20:30'), true],
            [new DateTime('2015-03-01 04:00:00'), false],
            [new DateTime('2015-05-01 04:40:00'), false],
        ];
    }

    public function testGetSlotCount()
    {
        $set = new RequirementSet();
        $set->setBegin(new DateTime('2015-05-01 06:00:00'));
        $set->setEnd(new DateTime('2015-05-03 08:00:00'));
        $set->setSlotDuration(20 * 60);

        $this->assertSame((24 + 24 + 2) * 3, $set->getSlotCount());
    }
}
