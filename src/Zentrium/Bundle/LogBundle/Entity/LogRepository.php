<?php

namespace Zentrium\Bundle\LogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Doctrine\ColumnHydrator;

class LogRepository extends EntityRepository
{
    public function findByStatusWithLabels($status, array $labels)
    {
        $qb = $this->createQueryBuilder('log')
            ->addSelect('label')
            ->orderBy('log.updated', 'DESC')
            ->leftJoin('log.labels', 'label')
        ;

        if ($status !== null) {
            $qb
                ->where('log.status = :status')
                ->setParameter('status', $status)
            ;
        }

        if (!empty($labels)) {
            $qb
                ->andWhere('label.id IN (:labels)')
                ->groupBy('log.id')
                ->having('COUNT(label.id) = :labelCount')
                ->setParameter('labels', $labels)
                ->setParameter('labelCount', count($labels));
        }

        return $qb->getQuery()->getResult();
    }

    public function aggregateByStatus()
    {
        $qb = $this->createQueryBuilder('l', 'l.status')
            ->select('l.status')
            ->addSelect('COUNT(l)')
            ->groupBy('l.status');

        return $qb->getQuery()->getResult(ColumnHydrator::NAME);
    }

    public function countComments()
    {
        $qb = $this->createQueryBuilder('l', 'l.id')
            ->select('l.id')
            ->addSelect('COUNT(c.id)')
            ->leftJoin('l.comments', 'c')
            ->groupBy('l.id');

        return $qb->getQuery()->getResult(ColumnHydrator::NAME);
    }
}
