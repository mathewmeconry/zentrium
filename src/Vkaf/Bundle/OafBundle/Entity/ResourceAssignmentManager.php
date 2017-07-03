<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.resource', 'r')
            ->orderBy('a.assignedAt', 'DESC')
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