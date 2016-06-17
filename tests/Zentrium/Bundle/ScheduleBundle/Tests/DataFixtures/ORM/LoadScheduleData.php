<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;

class LoadScheduleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $schedule = new Schedule();
        $schedule->setName('First Schedule');
        $schedule->setBegin(new DateTime('2010-01-01 12:00:00'));
        $schedule->setEnd(new DateTime('2010-01-05 18:00:00'));
        $schedule->setSlotDuration(3600);
        $manager->persist($schedule);
        $this->addReference('schedule-basic', $schedule);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
