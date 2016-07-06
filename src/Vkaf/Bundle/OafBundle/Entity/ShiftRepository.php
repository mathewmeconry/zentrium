<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\EntityManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;

class ShiftRepository
{
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository(Shift::class);
    }

    public function findAdjacent(Schedule $schedule, DateTimeInterface $date)
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->addSelect('t')
            ->addSelect('u')
            ->leftJoin('s.user', 'u')
            ->leftJoin('s.task', 't')
            ->where('s.schedule = :schedule')
            ->andWhere('(s.from = :date OR s.to = :date)')
            ->setParameter('schedule', $schedule)
            ->setParameter('date', $date)
        ;

        return $qb->getQuery()->getResult();
    }
}
