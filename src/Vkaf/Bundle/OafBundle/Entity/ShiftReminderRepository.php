<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\Shift;

class ShiftReminderRepository extends EntityRepository
{
    public function findUpcoming($limit)
    {
        $now = new DateTime();

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('s.from')
            ->addSelect('u.id AS user')
            ->from(Shift::class, 's')
            ->leftJoin('s.schedule', 'p')
            ->leftJoin('s.task', 't')
            ->leftJoin('s.user', 'u')
            ->where('p.published = 1')
            ->andWhere('t.informative = 0')
            ->andWhere('s.from > :now')
            ->andWhere('s.from <= :limit')
            ->andWhere($qb->expr()->not($qb->expr()->exists(
                $em
                    ->createQueryBuilder()
                    ->select('s2.id')
                    ->from(Shift::class, 's2')
                    ->leftJoin('s2.schedule', 'p2')
                    ->leftJoin('s2.task', 't2')
                    ->where('s2.user = s.user')
                    ->andWhere('p2.published = 1')
                    ->andWhere('t2.informative = 0')
                    ->andWhere('s2.from < s.from')
                    ->andWhere('s2.to >= s.from')
            )))
            ->andWhere($qb->expr()->not($qb->expr()->exists(
                $em
                    ->createQueryBuilder()
                    ->select('r.id')
                    ->from(ShiftReminder::class, 'r')
                    ->where('r.user = s.user')
                    ->andWhere('r.from = s.from')
            )))
            ->groupBy('u')
            ->addGroupBy('s.from')
            ->setParameter('now', $now)
            ->setParameter('limit', $limit)
            ->getQuery()
            ->execute()
        ;
    }
}
