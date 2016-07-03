<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

class LoadEntryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $entry = new Entry();
        $entry->setUser($this->getReference('user-viewer'));
        $entry->setStart(new DateTime('2015-04-01 12:00:00'));
        $entry->setEnd(new DateTime('2015-04-01 15:00:00'));
        $entry->setActivity($this->getReference('activity-a'));
        $entry->setAuthor($this->getReference('user-manager'));
        $entry->setNotes('Notes A');
        $manager->persist($entry);
        $this->addReference('entry-a', $entry);

        $entry = new Entry();
        $entry->setUser($this->getReference('user-viewer'));
        $entry->setStart(new DateTime('2015-04-02 19:00:00'));
        $entry->setEnd(new DateTime('2015-04-02 20:15:00'));
        $entry->setActivity($this->getReference('activity-b'));
        $entry->setAuthor($this->getReference('user-manager'));
        $manager->persist($entry);
        $this->addReference('entry-b', $entry);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
