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

    public function findPresent()
    {
        $qb = $this->createSortedQueryBuilder()
            ->where('u.present = 1')
        ;

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

    public function findOneByCanonicalEmail($email)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.emailCanonical = :email')
            ->setParameter('email', $email)
            ->setMaxResults(2);

        $results = $qb->getQuery()->getResult();

        return count($results) === 1 ? $results[0] : null;
    }

    public function createSortedQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
        ;
    }
}
