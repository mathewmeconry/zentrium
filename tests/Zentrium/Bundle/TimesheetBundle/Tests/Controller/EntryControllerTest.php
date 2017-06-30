<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests\Controller;

use Zentrium\Bundle\TimesheetBundle\Tests\WebTestCase;

class EntryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/timesheet/');

        $this->assertCount(2, $crawler->filter('.content tbody tr'));
    }

    public function testExport()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/timesheet/export');
        $form = $crawler->selectButton('export_parameters[export]')->form([
            'export_parameters[format]' => 'csv',
            'export_parameters[from]' => '01.01.2000',
            'export_parameters[to]' => '01.01.2020',
        ]);

        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertSame('text/comma-separated-values; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));

        $lines = explode("\n", $client->getResponse()->getContent());
        $this->assertCount(4, $lines); // including empty line at the end
        $this->assertTrue(0 === strpos($lines[1], '1,"2015-04-01 12:00:00","2015-04-01 15:00:00",3.00,Carlos,Juan,"Activity A","Notes A",'));
    }
}
