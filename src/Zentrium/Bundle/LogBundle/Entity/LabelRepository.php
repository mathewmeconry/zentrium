<?php

namespace Zentrium\Bundle\LogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LabelRepository extends EntityRepository
{
    public function createSortedQueryBuilder()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.name', 'ASC');
    }

    public function findAll()
    {
        $qb = $this->createSortedQueryBuilder();

        return $qb->getQuery()->getResult();
    }
}
