<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Entity\User as ScheduleUser;

class UserRepository
{
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    public function findActive()
    {
        $qb = $this->repository->createQueryBuilder('u')
            ->leftJoin(Shift::class, 's', Join::WITH, 's.user = u.id')
            ->leftJoin('s.schedule', 'p')
            ->leftJoin('s.task', 't')
            ->where('s.from <= :now')
            ->andWhere('s.to > :now')
            ->andWhere('t.informative = 0')
            ->andWhere('p.published = 1')
            ->setParameter('now', new DateTime())
        ;

        return $qb->getQuery()->getResult();
    }

    public function findPresent()
    {
        $qb = $this->repository->createQueryBuilder('u')
            ->leftJoin(ScheduleUser::class, 's', Join::WITH, 's.base = u.id')
            ->leftJoin('s.availabilities', 'a')
            ->where('a.from <= :now')
            ->andWhere('a.to > :now')
            ->andWhere('u.present = 1')
            ->setParameter('now', new DateTime())
        ;

        return $qb->getQuery()->getResult();
    }
}
