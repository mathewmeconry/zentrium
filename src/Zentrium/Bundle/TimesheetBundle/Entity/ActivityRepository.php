<?php

namespace Zentrium\Bundle\TimesheetBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{
    public function createSortedQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.name')
        ;
    }
}
