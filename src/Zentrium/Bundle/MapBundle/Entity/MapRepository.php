<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MapRepository extends EntityRepository
{
    public function findWithLayers($id)
    {
        $qb = $this->createQueryBuilder('m')
            ->addSelect('s')
            ->addSelect('l')
            ->leftJoin('m.layers', 's')
            ->leftJoin('s.layer', 'l')
            ->where('m.id = :id')
            ->orderBy('s.position', 'ASC')
            ->setParameter('id', $id);

        return $qb->getQuery()->getSingleResult();
    }
}
