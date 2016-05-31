<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ScheduleManager
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
        $this->repository = $em->getRepository(Schedule::class);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function save(Schedule $schedule)
    {
        $this->em->transactional(function (EntityManager $em) use ($schedule) {
            $em->persist($schedule);
        });
    }
}
