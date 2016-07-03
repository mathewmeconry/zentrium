<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\TimesheetBundle\Entity\Activity;

class LoadActivityData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $activity = new Activity();
        $activity->setName('Activity A');
        $manager->persist($activity);
        $this->addReference('activity-a', $activity);

        $activity = new Activity();
        $activity->setName('Activity B');
        $manager->persist($activity);
        $this->addReference('activity-b', $activity);

        $manager->flush();
    }

    public function getOrder()
    {
        return 0;
    }
}
