<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Vkaf\Bundle\OafBundle\Security\TokenRepository;

class TerminalRepository extends EntityRepository implements TokenRepository
{
    public function findAll()
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.label')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOneByToken(string $token)
    {
        return parent::findOneByToken($token);
    }
}
