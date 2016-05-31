<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\ScheduleBundle\RequirementSet\OperationInterface;

class RequirementSetManager
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
        $this->repository = $em->getRepository(RequirementSet::class);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function getTasks(RequirementSet $set)
    {
        $query = $this->em
            ->createQuery('SELECT t FROM '.Task::class.' t WHERE t.id IN(SELECT t2.id FROM '.Requirement::class.' r LEFT JOIN r.set s LEFT JOIN r.task t2 WHERE s.id = :set)')
            ->setParameter('set', $set->getId())
        ;

        return $query->getResult();
    }

    public function apply(RequirementSet $set, OperationInterface $operation)
    {
        $operation->apply($set);
        $set->triggerUpdate();

        $this->em->transactional(function (EntityManager $em) use ($set) {
            $em->persist($set);
        });

        return $set;
    }
}
