<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

class SkillRepository extends EntityRepository
{
    public function findAllWithUserCounts()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('COUNT(u.base)')
            ->leftJoin('s.users', 'u')
            ->orderBy('s.name', 'ASC')
            ->groupBy('s.id')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findWithUsers($id)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('u')
            ->addSelect('b')
            ->leftJoin('s.users', 'u')
            ->leftJoin(BaseUser::class, 'b', 'WITH', 'b.id = u.base')
            ->where('s.id = :id')
            ->orderBy('b.lastName', 'ASC')
            ->addOrderBy('b.firstName', 'ASC')
            ->setParameter('id', $id)
        ;

        $result = $qb->getQuery()->getResult();

        return (!empty($result) ? $result[0] : null);
    }
}
