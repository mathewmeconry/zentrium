<?php

namespace Zentrium\Bundle\CoreBundle\Tests;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class WebTestCase extends BaseWebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $references;

    protected function getAllFixtureClasses()
    {
        return [
            'Zentrium\Bundle\CoreBundle\Tests\DataFixtures\ORM\LoadUserData',
            'Zentrium\Bundle\CoreBundle\Tests\DataFixtures\ORM\LoadApplicationData',
        ];
    }

    protected function loadAllFixtures()
    {
        $fixtures = $this->loadFixtures($this->getAllFixtureClasses());

        $this->references = $fixtures->getReferenceRepository();
    }

    protected function loginByReference($reference, $firewall = 'main')
    {
        $user = $this->references->getReference($reference);

        return static::loginAs($user, $firewall);
    }

    protected function makeApiClient()
    {
        $client = self::createClient();
        $server = $client->getContainer()->get('fos_oauth_server.server');
        $application = $this->references->getReference('application');

        // Create a fake request to get a token
        $request = Request::create('', 'GET', [
            'client_id' => $application->getPublicId(),
            'client_secret' => $application->getSecret(),
            'grant_type' => 'client_credentials',
        ]);

        $response = $server->grantAccessToken($request);
        $content = json_decode($response->getContent());

        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer '.$content->access_token);

        return $client;
    }
}
