<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Schedule\Constraint\Validator;

use DateTimeImmutable;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Constraint;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator\PauseValidator;

class PauseValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    public function setUp()
    {
        $dateTimeHelper = $this->getMockBuilder('\Zentrium\Bundle\CoreBundle\Templating\Helper\DateTimeHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $dateTimeHelper->method('format')->will($this->returnCallback(function ($date) {
            return $date->format('Y-m-d H:i:s');
        }));

        $durationHelper = $this->getMockBuilder('\Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $durationHelper->method('format')->will($this->returnCallback(function ($duration) {
            return (string) $duration;
        }));

        $this->validator = new PauseValidator($dateTimeHelper, $durationHelper);
    }

    public function testSparseSchedule()
    {
        $schedule = new Schedule();
        $schedule->setBegin(new DateTimeImmutable('2015-05-01 06:00:00'));
        $schedule->setEnd(new DateTimeImmutable('2015-05-05 18:00:00'));
        $schedule->setSlotDuration(3600);

        $user = $this->getMock('Zentrium\Bundle\CoreBundle\Entity\User');
        $user->method('getId')->willReturn(1);
        $user->method('getName')->willReturn('name');

        $shiftStart = new DateTimeImmutable('2015-05-02 12:00:00');
        for ($i = 0; $i < 4; $i++) { // => working slots: ...X.....X.....X.....X...
            $shift = new Shift();
            $shift->setUser($user);
            $shift->setFrom($shiftStart);
            $shift->setTo($shiftStart->modify('+ 1 hour'));
            $schedule->getShifts()->add($shift);
            $shiftStart = $shiftStart->modify('+ 6 hours');
        }

        $constraint = new Constraint('pause', 'Test', ['limit' => 3 * 3600, 'pause' => 6 * 3600]);

        $messages = $this->validator->validate($schedule, $constraint);

        $this->assertCount(1, $messages);
        $this->assertEquals(new \DateTime('2015-05-02 12:00:00'), $messages[0]->getPeriod()->getStartDate());
        $this->assertEquals(new \DateTime('2015-05-03 07:00:00'), $messages[0]->getPeriod()->getEndDate());
    }

    public function testSingleShift()
    {
        $schedule = new Schedule();
        $schedule->setBegin(new DateTimeImmutable('2015-05-01 06:00:00'));
        $schedule->setEnd(new DateTimeImmutable('2015-05-05 18:00:00'));
        $schedule->setSlotDuration(3600);

        $user = $this->getMock('Zentrium\Bundle\CoreBundle\Entity\User');
        $user->method('getId')->willReturn(1);
        $user->method('getName')->willReturn('name');

        $shift = new Shift();
        $shift->setUser($user);
        $shift->setFrom(new DateTimeImmutable('2015-05-02 12:00:00'));
        $shift->setTo(new DateTimeImmutable('2015-05-02 18:00:00'));
        $schedule->getShifts()->add($shift);

        $constraint = new Constraint('pause', 'Test', ['limit' => 3 * 3600, 'pause' => 6 * 3600]);

        $messages = $this->validator->validate($schedule, $constraint);

        $this->assertCount(1, $messages);
        $this->assertEquals(new \DateTime('2015-05-02 12:00:00'), $messages[0]->getPeriod()->getStartDate());
        $this->assertEquals(new \DateTime('2015-05-02 18:00:00'), $messages[0]->getPeriod()->getEndDate());
    }
}
