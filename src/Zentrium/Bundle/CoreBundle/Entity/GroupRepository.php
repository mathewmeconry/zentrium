<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    public function findAll()
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.name');

        return $qb->getQuery()->getResult();
    }

    public function createSortedQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.name')
        ;
    }
}
