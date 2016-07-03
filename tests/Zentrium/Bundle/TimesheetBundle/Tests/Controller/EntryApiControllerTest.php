<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\Controller;

use Zentrium\Bundle\TimesheetBundle\Tests\WebTestCase;

class EntryApiControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/timesheet/entries');
        $this->assertStatusCode(200, $client);

        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $activities);
    }

    public function testGet()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/timesheet/entries/1');
        $this->assertStatusCode(200, $client);

        $activity = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Notes A', $activity['notes']);
        $this->assertSame(2, $activity['author_id']);
    }
}
