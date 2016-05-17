<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAll()
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.name')
            ->addOrderBy('u.firstname');

        return $qb->getQuery()->getResult();
    }

    public function count()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}