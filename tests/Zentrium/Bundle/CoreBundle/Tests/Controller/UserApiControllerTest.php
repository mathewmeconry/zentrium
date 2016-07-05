<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Controller;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class UserApiControllerTest extends WebTestCase
{
    public function testList()
    {
        $this->loadAllFixtures();
        $client = static::makeApiClient();

        $client->request('GET', '/api/users');
        $this->assertStatusCode(200, $client);

        $users = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(4, $users);
        $this->assertCount(3, array_keys($users[0]));
    }
}
