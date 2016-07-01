<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Requirement;
use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;

class LoadRequirementSetData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $set = new RequirementSet();
        $set->setName('First Schedule');
        $set->setBegin(new DateTime('2010-01-01 06:00:00'));
        $set->setEnd(new DateTime('2010-01-10 12:00:00'));
        $set->setSlotDuration(3600);
        $this->addRequirement($set, 'task-a', 3, '2010-01-01 06:00:00', '2010-01-01 12:00:00');
        $this->addRequirement($set, 'task-a', 1, '2010-01-01 12:00:00', '2010-01-03 06:00:00');
        $this->addRequirement($set, 'task-b', 2, '2010-01-01 12:00:00', '2010-01-01 18:00:00');
        $manager->persist($set);
        $this->addReference('set-basic', $set);

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }

    private function addRequirement(RequirementSet $set, $taskReference, $count, $from, $to)
    {
        $requirement = new Requirement();
        $requirement->setTask($this->getReference($taskReference));
        $requirement->setCount($count);
        $requirement->setFrom(new DateTime($from));
        $requirement->setTo(new DateTime($to));
        $requirement->setSet($set);
        $set->getRequirements()->add($requirement);

        return $requirement;
    }
}
