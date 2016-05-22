<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class WmtsLayerManager
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
        $this->repository = $em->getRepository(WmtsLayer::class);
    }

    public function findByCapabilitiesUrl($capabilitiesUrl)
    {
        return $this->repository->findBy([
            'capabilitiesUrl' => $capabilitiesUrl,
        ]);
    }

    public function findOneByCapabilitiesUrlAndLayerId($capabilitiesUrl, $layerId)
    {
        return $this->repository->findOneBy([
            'capabilitiesUrl' => $capabilitiesUrl,
            'layerId' => $layerId,
        ]);
    }

    public function upsert(WmtsLayer $layer)
    {
        if ($layer->getId() === null) {
            $existing = $this->repository->findOneBy([
                'capabilitiesUrl' => $layer->getCapabilitiesUrl(),
                'layerId' => $layer->getLayerId(),
            ]);

            if ($existing) {
                $existing->setName($layer->getName());
                $existing->setCapabilities($layer->getCapabilities());
                $layer = $existing;
            }
        }

        $this->em->transactional(function (EntityManager $em) use ($layer) {
            $em->persist($layer);
        });

        return $layer;
    }

    public function delete(WmtsLayer $layer)
    {
        return $this->deleteMultiple([$layer]);
    }

    public function deleteMultiple(array $layers)
    {
        $this->em->transactional(function (EntityManager $em) use ($layers) {
            foreach ($layers as $layer) {
                $em->remove($layer);
            }
        });
    }
}
