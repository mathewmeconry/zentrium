<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class SkillManager
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
        $this->repository = $em->getRepository(Skill::class);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function findAllWithUserCounts()
    {
        return $this->repository->findAllWithUserCounts();
    }

    public function save(Skill $skill)
    {
        $this->em->transactional(function (EntityManager $em) use ($skill) {
            $em->persist($skill);
        });
    }
}
