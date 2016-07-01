<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Controller;

use Zentrium\Bundle\ScheduleBundle\Tests\WebTestCase;

class TaskApiControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/schedules/tasks');
        $this->assertStatusCode(200, $client);

        $tasks = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $tasks);
    }

    public function testGet()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/schedules/tasks/1');
        $this->assertStatusCode(200, $client);

        $task = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Task A', $task['name']);
        $this->assertSame('Skill A', $task['skill']['name']);
    }
}
