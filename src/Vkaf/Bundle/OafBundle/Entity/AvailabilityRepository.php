<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityManager;
use Zentrium\Bundle\ScheduleBundle\Entity\Availability;

class AvailabilityRepository
{
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository(Availability::class);
    }

    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->addSelect('s')
            ->addSelect('u')
            ->leftJoin('a.user', 's')
            ->leftJoin('s.base', 'u')
        ;

        return $qb->getQuery()->getResult();
    }
}
