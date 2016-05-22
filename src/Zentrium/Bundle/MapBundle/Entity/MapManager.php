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
        $query = $this->repository->createQueryBuilder()
            ->update('m')
            ->set('default', '(m.id != :id)')
            ->setParameter('id', $map->getId());

        $query->execute();

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
}
