<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Controller;

use Zentrium\Bundle\ScheduleBundle\Tests\WebTestCase;

class ConstraintControllerTest extends WebTestCase
{
    public function testNew()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-scheduler');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/schedules/constraints/new');
        $this->assertStatusCode(200, $client);

        $typeLinks = $crawler->filter('.content li a')->each(function ($node) {
            return $node->link();
        });
        $this->assertGreaterThanOrEqual(5, count($typeLinks));

        foreach ($typeLinks as $typeLink) {
            $client->click($typeLink);
            $this->assertStatusCode(200, $client);
        }
    }

    public function testNewAsManager()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/schedules/constraints/new');
        $this->assertStatusCode(403, $client);
    }
}
