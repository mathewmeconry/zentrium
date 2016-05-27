<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class TaskManager
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
        $this->repository = $em->getRepository(Task::class);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['code' => 'ASC']);
    }

    public function save(Task $task)
    {
        $this->em->transactional(function (EntityManager $em) use ($task) {
            $em->persist($task);
        });
    }
}
