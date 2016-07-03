<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\Controller;

use Zentrium\Bundle\TimesheetBundle\Tests\WebTestCase;

class ActivityApiControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/timesheet/activities');
        $this->assertStatusCode(200, $client);

        $activities = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $activities);
    }
}
