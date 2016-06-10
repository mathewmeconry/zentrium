<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Controller;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $this->loadAllFixtures();
        $client = static::makeClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');
        $this->assertSame('/login', $client->getRequest()->getPathInfo());

        $loginForm = $crawler->selectButton('_submit')->form();
        $loginForm['_username']->setValue('manager');
        $loginForm['_password']->setValue('manager');

        $client->submit($loginForm);
        $this->assertStatusCode(200, $client);
        $this->assertSame('/', $client->getRequest()->getPathInfo());
    }

    public function testLoginInvalid()
    {
        $this->loadAllFixtures();
        $client = static::makeClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');
        $loginForm = $crawler->selectButton('_submit')->form();
        $loginForm['_username']->setValue('manager');
        $loginForm['_password']->setValue('invalid');

        $client->submit($loginForm);
        $this->assertStatusCode(200, $client);
        $this->assertSame('/login', $client->getRequest()->getPathInfo());
    }

    public function testLoginDisabled()
    {
        $this->loadAllFixtures();
        $client = static::makeClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');
        $loginForm = $crawler->selectButton('_submit')->form();
        $loginForm['_username']->setValue('disabled');
        $loginForm['_password']->setValue('disabled');

        $client->submit($loginForm);
        $this->assertStatusCode(200, $client);
        $this->assertSame('/login', $client->getRequest()->getPathInfo());
    }
}
