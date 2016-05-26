<?php

namespace Zentrium\Bundle\MapBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Zentrium\Bundle\MapBundle\Entity\MapLayer;

class MapLayerCollectionToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var Collection
     */
    private $collection;

    public function __construct(ObjectRepository $repository, Collection $collection = null)
    {
        $this->repository = $repository;
        $this->collection = ($collection !== null ? $collection : new ArrayCollection());
    }

    /**
     * {@inheritdoc}
     */
    public function transform($layers)
    {
        if (null === $layers) {
            return json_encode([]);
        }

        $layerList = [];
        foreach ($layers as $layer) {
            $layerList[] = [$layer->getLayer()->getId(), $layer->getPosition()];
        }
        usort($layerList, function ($a, $b) {
            return $a[1] - $b[1];
        });

        $layerIds = array_map(function ($row) {
            return $row[0];
        }, $layerList);

        return json_encode($layerIds);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    {
        $array = json_decode($array, true);
        if (!is_array($array)) {
            $this->collection->clear();

            return $this->collection;
        }

        $array = array_map('intval', $array);

        $toDelete = [];
        $existing = [];
        foreach ($this->collection as $key => $mapLayer) {
            $id = $mapLayer->getLayer()->getId();
            if (!in_array($id, $array)) {
                $mapLayer->setMap(null);
                $this->collection->remove($key);
            } else {
                $mapLayer->setPosition(array_search($id, $array));
                $existing[] = $id;
            }
        }

        foreach ($array as $position => $id) {
            if (!in_array($id, $existing)) {
                $layer = $this->repository->find($id);
                if ($layer === null) {
                    continue;
                }
                $mapLayer = new MapLayer();
                $mapLayer->setLayer($layer);
                $mapLayer->setPosition($position);
                $this->collection->add($mapLayer);
            }
        }

        return $this->collection;
    }
}
