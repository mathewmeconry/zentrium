<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ShiftManager
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
        $this->repository = $em->getRepository(Shift::class);
    }

    public function save(Shift $shift)
    {
        $this->em->transactional(function (EntityManager $em) use ($shift) {
            $em->persist($shift);
        });
    }

    public function delete(Shift $shift)
    {
        $this->em->transactional(function (EntityManager $em) use ($shift) {
            $em->remove($shift);
        });
    }
}
