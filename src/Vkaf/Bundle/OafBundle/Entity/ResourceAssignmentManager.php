<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;

class ResourceAssignmentManager
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
        $this->repository = $em->getRepository(ResourceAssignment::class);
    }

    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->addSelect('r')
            ->addSelect('u')
            ->addSelect('(CASE WHEN a.returnedAt IS NULL THEN 1 ELSE 0 END) AS HIDDEN returned')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.resource', 'r')
            ->orderBy('returned', 'DESC')
            ->addOrderBy('r.label', 'ASC')
            ->addOrderBy('a.assignedAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findNonReturnedByUser(User $user)
    {
        $qb = $this->repository->createQueryBuilder('a')
            ->addSelect('r')
            ->leftJoin('a.resource', 'r')
            ->where('a.user = :user')
            ->andWhere('a.returnedAt IS NULL')
            ->orderBy('a.assignedAt', 'DESC')
            ->setParameter('user', $user)
        ;

        return $qb->getQuery()->getResult();
    }

    public function save(ResourceAssignment $assignment)
    {
        $this->em->transactional(function (EntityManager $em) use ($assignment) {
            $em->persist($assignment);
        });
    }
}
