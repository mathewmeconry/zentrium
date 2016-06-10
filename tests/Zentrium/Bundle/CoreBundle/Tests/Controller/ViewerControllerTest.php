<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Controller;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class ViewerControllerTest extends WebTestCase
{
    public function testProfile()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-viewer');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/viewer/profile');
        $this->assertStatusCode(200, $client);
        $this->assertSame(1, $crawler->filterXpath('//dd[text()="033 444 55 66"]')->count());
    }
}
