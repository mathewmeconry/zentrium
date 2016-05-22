<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\Client;
use OAuth2\OAuth2;

/**
 * @ORM\Entity
 */
class Application extends Client
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();

        $this->allowedGrantTypes = [OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS];
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getRandomId();
    }
}
