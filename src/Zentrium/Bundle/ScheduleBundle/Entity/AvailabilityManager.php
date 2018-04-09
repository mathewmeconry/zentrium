<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

class AvailabilityManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Availability::class);
    }

    public function findOverlapping(Period $period)
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->where('a.from < :end')
            ->andWhere('a.to > :begin')
            ->setParameter('begin', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOverlappingByUser(Period $period, User $user)
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->where('a.from < :end')
            ->andWhere('a.to > :begin')
            ->andWhere('a.user = :user')
            ->setParameter('begin', $period->getStartDate())
            ->setParameter('end', $period->getEndDate())
            ->setParameter('user', $user)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findUpcomingByUser(BaseUser $user)
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.to >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new DateTime())
        ;

        return $qb->getQuery()->getResult();
    }

    public function save(Availability $availability)
    {
        $this->em->transactional(function (EntityManager $em) use ($availability) {
            $em->persist($availability);
        });
    }
}
