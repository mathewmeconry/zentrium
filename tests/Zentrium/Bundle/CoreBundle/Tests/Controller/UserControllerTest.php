<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Controller;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();

        $crawler = $client->request('GET', '/users');
        $this->assertStatusCode(200, $client);

        $rowCount = $crawler->filter('.content tbody tr')->count();
        $this->assertGreaterThanOrEqual(4, $rowCount);
    }

    public function testEditAsManager()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-manager');
        $client = static::makeClient();
        $viewerId = $this->references->getReference('user-viewer')->getId();

        $crawler = $client->request('GET', '/users/'.$viewerId.'/edit');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('user[save]')->form();
        $this->assertFalse($form->has('user[email]'));
        $this->assertFalse($form->has('user[enabled]'));

        $crawler = $client->submit($form);
        $this->assertStatusCode(302, $client);
    }

    public function testEditAsAdmin()
    {
        $this->loadAllFixtures();
        $this->loginByReference('user-admin');
        $client = static::makeClient();
        $viewerId = $this->references->getReference('user-viewer')->getId();

        $crawler = $client->request('GET', '/users/'.$viewerId.'/edit');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('user_account[save]')->form();
        $this->assertTrue($form->has('user_account[email]'));
        $this->assertTrue($form->has('user_account[enabled]'));

        $crawler = $client->submit($form);
        $this->assertStatusCode(302, $client);
    }
}
