<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializerInterface;
use LogicException;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Constraint as BasicConstraint;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;

class ConstraintManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(EntityManager $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Constraint::class);
        $this->serializer = $serializer;
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function findMultiple(array $ids)
    {
        return $this->repository->findBy(['id' => $ids], ['name' => 'ASC']);
    }

    public function load($id)
    {
        $entity = $this->repository->find($id);
        if ($entity === null) {
            return null;
        }

        return $this->deserialize($entity);
    }

    public function loadMultiple(array $ids)
    {
        $entities = $this->findMultiple($ids);
        $constraints = [];
        foreach ($entities as $entity) {
            $constraints[] = $this->deserialize($entity);
        }

        return $constraints;
    }

    public function save(ConstraintInterface $constraint, Constraint $entity = null)
    {
        if ($entity === null) {
            $entity = new Constraint();
        }

        if (is_object($constraint->getParameters())) {
            $parametersType = get_class($constraint->getParameters());
        } elseif (is_array($constraint->getParameters())) {
            $parametersType = 'array';
        } else {
            throw new LogicException('Unknown type.');
        }

        $entity->setType($constraint->getType());
        $entity->setName($constraint->getName());
        $entity->setParameters($this->serializer->serialize($constraint->getParameters(), 'json'));
        $entity->setParametersType($parametersType);

        $this->em->transactional(function (EntityManager $em) use ($entity) {
            $em->persist($entity);
        });

        return $entity;
    }

    public function deserialize(Constraint $entity)
    {
        $parameters = $this->serializer->deserialize($entity->getParameters(), $entity->getParametersType(), 'json');

        return new BasicConstraint($entity->getType(), $entity->getName(), $parameters);
    }
}
