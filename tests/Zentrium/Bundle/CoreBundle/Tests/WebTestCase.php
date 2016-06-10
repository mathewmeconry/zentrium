<?php

namespace Zentrium\Bundle\CoreBundle\Tests;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $references;

    protected function getAllFixtureClasses()
    {
        return [
            'Zentrium\Bundle\CoreBundle\Tests\DataFixtures\ORM\LoadUserData',
        ];
    }

    protected function loadAllFixtures()
    {
        $fixtures = $this->loadFixtures($this->getAllFixtureClasses());

        $this->references = $fixtures->getReferenceRepository();
    }

    protected function loginByReference($user, $firewall = 'main')
    {
        $user = $this->references->getReference($user);

        return static::loginAs($user, $firewall);
    }
}
