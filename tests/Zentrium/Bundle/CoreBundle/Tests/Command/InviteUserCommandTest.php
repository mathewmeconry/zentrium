<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Command;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class InviteUserCommandTest extends WebTestCase
{
    public function testInvite()
    {
        $this->loadAllFixtures();

        $url = trim($this->runCommand('zentrium:user:invite', ['username' => 'viewer'], true));
        $this->assertRegExp('#^https?://#', $url);

        $client = static::makeClient();
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);
    }
}
