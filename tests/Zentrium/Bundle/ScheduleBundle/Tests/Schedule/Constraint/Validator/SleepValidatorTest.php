<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Schedule\Constraint\Validator;

use DateTime;
use League\Period\Period;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Constraint;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator\SleepValidator;

class SleepValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    public function setUp()
    {
        $dateTimeHelper = $this->createMock('\Zentrium\Bundle\CoreBundle\Templating\Helper\DateTimeHelper');
        $dateTimeHelper->method('format')->will($this->returnCallback(function ($date) {
            return $date->format('Y-m-d H:i:s');
        }));

        $this->validator = new SleepValidator($dateTimeHelper);
    }

    public function testBasic()
    {
        $schedule = new Schedule();
        $schedule->setBegin(new DateTime('2015-05-01 06:00:00'));
        $schedule->setEnd(new DateTime('2015-05-05 18:00:00'));
        $schedule->setSlotDuration(3600);

        $user = $this->mockUser(1, 'User1');

        $shiftA = new Shift();
        $shiftA->setUser($user);
        $shiftA->setFrom(new DateTime('2015-05-01 12:00:00'));
        $shiftA->setTo(new DateTime('2015-05-01 18:00:00'));
        $schedule->getShifts()->add($shiftA);

        $shiftB = new Shift();
        $shiftB->setUser($user);
        $shiftB->setFrom(new DateTime('2015-05-02 01:00:00'));
        $shiftB->setTo(new DateTime('2015-05-02 05:00:00'));
        $schedule->getShifts()->add($shiftB);

        $constraint = new Constraint('sleep', 'Test', ['minimum' => 8 * 3600]);

        $messages = $this->validator->validate($schedule, $constraint);

        $this->assertCount(1, $messages);
        $this->assertTrue($messages[0]->getPeriod()->sameValueAs(new Period('2015-05-01 12:00:00', '2015-05-02 12:00:00')));
    }

    public function testSleepAfterSchedule()
    {
        $schedule = new Schedule();
        $schedule->setBegin(new DateTime('2015-05-01 06:00:00'));
        $schedule->setEnd(new DateTime('2015-05-05 18:00:00'));
        $schedule->setSlotDuration(3600);

        $shift = new Shift();
        $shift->setUser($this->mockUser(1, 'User1'));
        $shift->setFrom(new DateTime('2015-05-05 12:00:00'));
        $shift->setTo(new DateTime('2015-05-05 18:00:00'));
        $schedule->getShifts()->add($shift);

        $constraint = new Constraint('sleep', 'Test', ['minimum' => 6 * 3600]);

        $this->assertCount(0, $this->validator->validate($schedule, $constraint));
    }

    public function testLongShift()
    {
        $schedule = new Schedule();
        $schedule->setBegin(new DateTime('2015-05-01 06:00:00'));
        $schedule->setEnd(new DateTime('2015-05-05 18:00:00'));
        $schedule->setSlotDuration(3600);

        $shift = new Shift();
        $shift->setUser($this->mockUser(1, 'User1'));
        $shift->setFrom(new DateTime('2015-05-02 18:00:00'));
        $shift->setTo(new DateTime('2015-05-04 22:00:00'));
        $schedule->getShifts()->add($shift);

        $constraint = new Constraint('sleep', 'Test', ['minimum' => 6 * 3600]);

        $messages = $this->validator->validate($schedule, $constraint);

        $this->assertCount(1, $messages);
        $this->assertTrue($messages[0]->getPeriod()->sameValueAs(new Period('2015-05-02 18:00:00', '2015-05-03 18:00:00')));
    }

    private function mockUser($id, $name)
    {
        $user = $this->createMock('Zentrium\Bundle\CoreBundle\Entity\User');
        $user->method('getId')->willReturn($id);
        $user->method('getName')->willReturn($name);

        return $user;
    }
}
