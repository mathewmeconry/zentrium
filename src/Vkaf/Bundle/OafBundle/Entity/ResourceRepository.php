<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ResourceRepository extends EntityRepository
{
    public function createSortedQueryBuilder()
    {
        return $this
            ->createQueryBuilder('r')
            ->addSelect('g')
            ->leftJoin('r.owner', 'g')
            ->orderBy('g.name')
            ->addOrderBy('r.label')
        ;
    }
}
