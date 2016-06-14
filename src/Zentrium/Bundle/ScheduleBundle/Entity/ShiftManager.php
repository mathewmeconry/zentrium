<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

class ShiftManager
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
        $this->repository = $em->getRepository(Shift::class);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findUpcomingByUser(BaseUser $user)
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->addSelect('t')
            ->leftJoin('s.schedule', 'p')
            ->leftJoin('s.task', 't')
            ->where('s.user = :user')
            ->andWhere('s.to >= :now')
            ->andWhere('p.published = 1')
            ->orderBy('s.from')
            ->setParameter('user', $user)
            ->setParameter('now', new DateTime())
        ;

        return $qb->getQuery()->getResult();
    }

    public function save(Shift $shift)
    {
        $this->em->transactional(function (EntityManager $em) use ($shift) {
            $em->persist($shift);
        });
    }

    public function delete(Shift $shift)
    {
        $this->em->transactional(function (EntityManager $em) use ($shift) {
            $em->remove($shift);
        });
    }
}
