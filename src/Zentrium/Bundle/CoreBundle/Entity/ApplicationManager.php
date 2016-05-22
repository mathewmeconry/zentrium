<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use FOS\OAuthServerBundle\Entity\ClientManager;

class ApplicationManager extends ClientManager
{
    /**
     * {@inheritdoc}
     */
    public function findClientByPublicId($publicId)
    {
        return $this->findClientBy(['randomId' => $publicId]);
    }
}
