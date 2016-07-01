<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Controller;

use Zentrium\Bundle\ScheduleBundle\Tests\WebTestCase;

class RequirementSetApiControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/schedules/requirements/sets');
        $this->assertStatusCode(200, $client);

        $sets = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $sets);
        $this->assertArrayNotHasKey('requirements', $sets[0]);
    }

    public function testGet()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/schedules/requirements/sets/1');
        $this->assertStatusCode(200, $client);

        $set = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('First Schedule', $set['name']);
        $this->assertArrayHasKey('requirements', $set);
    }
}
