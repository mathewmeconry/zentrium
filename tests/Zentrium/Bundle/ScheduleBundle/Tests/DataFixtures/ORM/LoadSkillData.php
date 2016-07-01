<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Skill;

class LoadSkillData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $skill = new Skill();
        $skill->setShortName('SA');
        $skill->setName('Skill A');
        $manager->persist($skill);
        $this->addReference('skill-a', $skill);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
