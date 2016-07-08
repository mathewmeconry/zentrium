<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ResourceManager
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
        $this->repository = $em->getRepository(Resource::class);
    }

    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('r')
            ->addSelect('g')
            ->leftJoin('r.owner', 'g')
            ->orderBy('g.name')
            ->addOrderBy('r.label')
        ;

        return $qb->getQuery()->getResult();
    }

    public function save(Resource $resource)
    {
        $this->em->transactional(function (EntityManager $em) use ($resource) {
            $em->persist($resource);
        });
    }
}