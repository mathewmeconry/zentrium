<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAll()
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName');

        return $qb->getQuery()->getResult();
    }

    public function findAllWithGroups()
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('g')
            ->leftJoin('u.groups', 'g')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName');

        return $qb->getQuery()->getResult();
    }

    public function count()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function createSortedQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
        ;
    }
}
