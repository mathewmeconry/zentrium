<?php

namespace Zentrium\Bundle\TimesheetBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class EntryManager
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
        $this->repository = $em->getRepository(Entry::class);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['start' => 'ASC']);
    }

    public function findByCriteria(Criteria $criteria)
    {
        $qb = $this->repository->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->leftJoin('u.groups', 'g')
            ->addCriteria($criteria)
        ;

        return $qb->getQuery()->getResult();
    }

    public function isOverlapping(Entry $entry)
    {
        $qb = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.start < :end')
            ->andWhere('e.end > :start')
            ->andWhere('e.user = :user')
            ->setParameter('start', $entry->getStart())
            ->setParameter('end', $entry->getEnd())
            ->setParameter('user', $entry->getUser())
        ;

        if ($entry->getId() !== null) {
            $qb->andWhere('e.id != :id')->setParameter('id', $entry->getId());
        }

        return intval($qb->getQuery()->getSingleScalarResult()) > 0;
    }

    public function save(Entry $entry)
    {
        $this->em->transactional(function (EntityManager $em) use ($entry) {
            $em->persist($entry);
        });
    }
}
