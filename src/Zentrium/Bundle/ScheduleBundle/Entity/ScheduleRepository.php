<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{
    public function findWithAssociations($id)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('p')
            ->addSelect('u')
            ->leftJoin('s.shifts', 'p')
            ->leftJoin('p.user', 'u')
            ->where('s.id = :id')
            ->orderBy('p.from', 'ASC')
            ->addOrderBy('p.to', 'ASC')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
