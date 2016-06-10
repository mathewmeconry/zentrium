<?php

namespace Zentrium\Bundle\CoreBundle\Tests\DataFixtures\ORM;

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
        $phonenumberUtil = $this->container->get('libphonenumber.phone_number_util');

        $adminUser = $userManager->createUser();
        $adminUser->setUsername('admin');
        $adminUser->setPlainPassword('admin');
        $adminUser->setEmail('admin@example.org');
        $adminUser->setFirstName('Bob');
        $adminUser->setLastName('Smith');
        $adminUser->setRoles(['ROLE_ADMINISTRATOR', 'ROLE_MANAGER']);
        $adminUser->setEnabled(true);
        $userManager->updateUser($adminUser);
        $this->addReference('user-admin', $adminUser);

        $managerUser = $userManager->createUser();
        $managerUser->setUsername('manager');
        $managerUser->setPlainPassword('manager');
        $managerUser->setEmail('manager@example.org');
        $managerUser->setFirstName('Sarah');
        $managerUser->setLastName('Jones');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setEnabled(true);
        $userManager->updateUser($managerUser);
        $this->addReference('user-manager', $managerUser);

        $viewerUser = $userManager->createUser();
        $viewerUser->setUsername('viewer');
        $viewerUser->setPlainPassword('viewer');
        $viewerUser->setEmail('viewer@example.org');
        $viewerUser->setMobilePhone($phonenumberUtil->parse('+41334445566', null));
        $viewerUser->setFirstName('Juan');
        $viewerUser->setLastName('Carlos');
        $viewerUser->setRoles([]);
        $viewerUser->setEnabled(true);
        $userManager->updateUser($viewerUser);
        $this->addReference('user-viewer', $viewerUser);

        $disabledUser = $userManager->createUser();
        $disabledUser->setUsername('disabled');
        $disabledUser->setPlainPassword('disabled');
        $disabledUser->setEmail('disabled@example.org');
        $disabledUser->setFirstName('David');
        $disabledUser->setLastName('Johnson');
        $disabledUser->setRoles(['ROLE_MANAGER']);
        $disabledUser->setEnabled(false);
        $userManager->updateUser($disabledUser);
        $this->addReference('user-disabled', $disabledUser);
    }

    public function getOrder()
    {
        return 0;
    }
}
