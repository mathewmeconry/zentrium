<?php

namespace Zentrium\Bundle\MapBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Zentrium\Bundle\MapBundle\Entity\Feature;
use Zentrium\Bundle\MapBundle\Position\PositionUpdateEvent;

class PositionUpdateListener
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onPositionUpdate(PositionUpdateEvent $event)
    {
        $position = $event->getPosition();

        $repository = $this->em->getRepository(Feature::class);
        $features = $repository->findByDevice($position->getDevice());
        foreach ($features as $feature) {
            if ($feature->getType() === Feature::TYPE_POINT) {
                $feature->setCoordinates([$position->getLongitude(), $position->getLatitude()]);
                $feature->setLastPosition($position);
                $this->em->persist($feature);
            }
        }
        $this->em->flush();
    }
}
