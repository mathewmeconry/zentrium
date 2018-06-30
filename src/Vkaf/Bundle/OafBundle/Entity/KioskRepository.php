<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Vkaf\Bundle\OafBundle\Security\TokenRepository;

class KioskRepository extends EntityRepository implements TokenRepository
{
    public function findOneByToken(string $token)
    {
        return parent::findOneByToken($token);
    }
}
