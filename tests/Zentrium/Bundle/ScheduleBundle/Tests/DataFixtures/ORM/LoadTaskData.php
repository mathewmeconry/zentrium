<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Task;

class LoadTaskData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $task = new Task();
        $task->setCode('TA');
        $task->setName('Task A');
        $task->setColor('#aaaaaa');
        $task->setSkill($this->getReference('skill-a'));
        $manager->persist($task);
        $this->addReference('task-a', $task);

        $task = new Task();
        $task->setCode('TB');
        $task->setName('Task B');
        $task->setColor('#bbbbbb');
        $manager->persist($task);
        $this->addReference('task-b', $task);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
