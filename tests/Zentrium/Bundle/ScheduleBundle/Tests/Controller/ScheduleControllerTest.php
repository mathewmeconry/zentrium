<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests\Controller;

use Zentrium\Bundle\ScheduleBundle\Tests\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/schedules/');
        $this->assertSame('First Schedule', $crawler->filter('.content td a')->text());
    }

    public function testNew()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/schedules/new');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('schedule[save]')->form([
            'schedule[name]' => 'Test',
            'schedule[begin][date]' => '01.05.2015',
            'schedule[begin][time]' => '12:00',
            'schedule[end][date]' => '03.05.2015',
            'schedule[end][time]' => '18:00',
            'schedule[slotDuration]' => '1:00',
        ]);

        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertSame('/schedules/2', $client->getRequest()->getPathInfo());
    }

    public function testValidateWithoutConstraints()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/schedules/1/validate');
        $this->assertStatusCode(200, $client);
    }

    public function testValidateResultWithoutConstraints()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $client->request('GET', '/schedules/1/validate/result.json');
        $this->assertStatusCode(200, $client);
        $this->assertSame([], json_decode($client->getResponse()->getContent(), true));
    }
}
