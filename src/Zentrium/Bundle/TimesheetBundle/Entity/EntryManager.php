<?php

namespace Zentrium\Bundle\TimesheetBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;

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
        $qb = $this->repository->createQueryBuilder('entry')
            ->addSelect('user')
            ->addSelect('activity')
            ->addSelect('author')
            ->leftJoin('entry.user', 'user')
            ->leftJoin('entry.author', 'author')
            ->leftJoin('entry.activity', 'activity')
            ->orderBy('entry.start', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByCriteria(Criteria $criteria)
    {
        $qb = $this->repository->createQueryBuilder('entry')
            ->addSelect('user')
            ->addSelect('activity')
            ->addSelect('author')
            ->leftJoin('entry.user', 'user')
            ->leftJoin('entry.author', 'author')
            ->leftJoin('entry.activity', 'activity')
            ->leftJoin('user.groups', 'groups')
            ->addCriteria($criteria)
        ;

        return $qb->getQuery()->getResult();
    }

    public function sumByUser(User $user, $approved = null)
    {
        $qb = $this->repository->createQueryBuilder('e')
            ->select('SUM(TIMESTAMP_DIFF(e.start, e.end))')
            ->where('e.user = :user')
            ->setParameter('user', $user)
        ;

        if ($approved === true) {
            $qb->andWhere('e.approvedAt IS NOT NULL');
        } elseif ($approved === false) {
            $qb->andWhere('e.approvedAt IS NULL');
        }

        return intval($qb->getQuery()->getSingleScalarResult());
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
