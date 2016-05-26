<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class MapManager
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
        $this->repository = $em->getRepository(Map::class);
    }

    public function setDefaultMap(Map $map)
    {
        $qb = $this->repository->createQueryBuilder('m')
            ->update()
            ->set('m.default', '(CASE WHEN m.id = :id THEN 1 ELSE 0 END)')
            ->setParameter('id', $map->getId());

        $qb->getQuery()->execute();

        $map->setDefault(true);
    }

    public function findDefault()
    {
        $defaults = $this->repository->findBy([], ['default' => 'DESC', 'name' => 'ASC'], 1);

        if (!count($defaults)) {
            return null;
        }

        return $defaults[0];
    }

    public function findAllWithNames()
    {
        $qb = $this->repository->createQueryBuilder('m')
            ->select('PARTIAL m.{id,name}')
            ->orderBy('m.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function save(Map $map)
    {
        $this->em->transactional(function (EntityManager $em) use ($map) {
            $em->persist($map);
        });
    }
}
