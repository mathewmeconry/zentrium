<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentrium\Bundle\MapBundle\Position\PositionEvents;
use Zentrium\Bundle\MapBundle\Position\PositionUpdateEvent;

class PositionManager
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Position::class);
        $this->dispatcher = $dispatcher;
    }

    public function update(Position $position)
    {
        $this->em->transactional(function (EntityManager $em) use ($position) {
            $em->persist($position);
        });
        $this->dispatcher->dispatch(PositionEvents::UPDATE, new PositionUpdateEvent($position));
    }
}
