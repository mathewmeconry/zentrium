<?php

namespace Zentrium\Bundle\TimesheetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ActivityManager
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
        $this->repository = $em->getRepository(Activity::class);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function save(Activity $activity)
    {
        $this->em->transactional(function (EntityManager $em) use ($activity) {
            $em->persist($activity);
        });
    }
}
