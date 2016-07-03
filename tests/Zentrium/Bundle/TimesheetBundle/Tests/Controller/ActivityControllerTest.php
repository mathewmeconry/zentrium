<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\Controller;

use Zentrium\Bundle\TimesheetBundle\Tests\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/timesheet/activities/');

        $this->assertCount(2, $crawler->filter('.content tbody tr'));
    }
}
