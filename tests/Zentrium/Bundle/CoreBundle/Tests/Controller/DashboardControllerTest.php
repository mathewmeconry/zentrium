<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Controller;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testDashboardAsViewer()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-viewer');
        $client = static::makeClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');
        $this->assertStringStartsWith('/viewer/', $client->getRequest()->getPathInfo());
    }

    public function testDashboard()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(200, $client);
        $this->assertGreaterThanOrEqual(4, intval($crawler->filterXPath('//div[contains(@class, "small-box") and .//a[@href="/users"]]//h3')->text()));
    }
}
