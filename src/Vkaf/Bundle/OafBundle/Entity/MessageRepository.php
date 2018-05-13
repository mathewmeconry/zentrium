<?php

namespace Vkaf\Bundle\OafBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;

class MessageRepository extends EntityRepository
{
    public function findAll()
    {
        return $this
            ->createQueryBuilder('m')
            ->addSelect('d')
            ->addSelect('u')
            ->leftJoin('m.deliveries', 'd')
            ->leftJoin('d.user', 'u')
            ->orderBy('m.created', 'DESC')
            ->addOrderBy('d.updated', 'DESC')
            ->getQuery()
            ->execute()
        ;
    }

    public function findByUser(User $user)
    {
        return $this
            ->createQueryBuilder('m')
            ->addSelect('d')
            ->addSelect('u')
            ->leftJoin('m.deliveries', 'd')
            ->leftJoin('d.user', 'u')
            ->where('m.id IN (SELECT m2.id FROM '.Message::class.' m2 LEFT JOIN m2.deliveries d2 WHERE d2.user = :user)')
            ->orderBy('m.created', 'DESC')
            ->addOrderBy('d.updated', 'DESC')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->execute()
        ;
    }

    public function findWithUsers($id)
    {
        return $this
            ->createQueryBuilder('m')
            ->addSelect('d')
            ->addSelect('u')
            ->leftJoin('m.deliveries', 'd')
            ->leftJoin('d.user', 'u')
            ->where('m.id = :id')
            ->orderBy('m.created', 'DESC')
            ->addOrderBy('d.updated', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
