<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $schedulerUser = $userManager->createUser();
        $schedulerUser->setUsername('scheduler');
        $schedulerUser->setPlainPassword('scheduler');
        $schedulerUser->setEmail('scheduler@example.org');
        $schedulerUser->setFirstName('Audrey');
        $schedulerUser->setLastName('Brown');
        $schedulerUser->setRoles(['ROLE_MANAGER', 'ROLE_SCHEDULER']);
        $schedulerUser->setEnabled(true);
        $userManager->updateUser($schedulerUser);
        $this->addReference('user-scheduler', $schedulerUser);
    }

    public function getOrder()
    {
        return 0;
    }
}
