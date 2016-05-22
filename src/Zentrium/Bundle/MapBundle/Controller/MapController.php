<?php

namespace Zentrium\Bundle\MapBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zentrium\Bundle\MapBundle\Entity\FeatureLayer;
use Zentrium\Bundle\MapBundle\Entity\Map;
use Zentrium\Bundle\MapBundle\Entity\WmtsLayer;

class MapController extends Controller
{
    /**
     * @Route("/map", name="maps")
     */
    public function indexAction()
    {
        $map = $this->get('zentrium_map.manager.map')->findDefault();

        return $this->redirectToRoute('map_view', ['map' => $map->getId()]);
    }

    /**
     * @Route("/map/{map}", name="map_view")
     * @ParamConverter("map", options={"repository_method" = "findWithLayers"})
     * @Template
     */
    public function viewAction(Map $map)
    {
        $config = [
            'layers' => [],
            'center' => $map->getCenter(),
            'zoom' => $map->getZoom(),
        ];

        foreach ($map->getLayers() as $layerSettings) {
            $layer = $layerSettings->getLayer();
            $layerConfig = [
                'enabled' => $layerSettings->isEnabled(),
                'opacity' => $layerSettings->getOpacity(),
                'position' => $layerSettings->getPosition(),
            ];
            if ($layer instanceof WmtsLayer) {
                $layerConfig['type'] = 'wmts';
                $layerConfig['capabilities'] = $layer->getCapabilities();
                $layerConfig['layerId'] = $layer->getLayerId();
            } elseif ($layer instanceof FeatureLayer) {
                $features = [];
                foreach ($layer->getFeatures() as $feature) {
                    $features[] = [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => $feature->getType(),
                            'coordinates' => $feature->getCoordinates(),
                        ],
                    ];
                }
                $layerConfig['type'] = 'feature';
                $layerConfig['features'] = $features;
            } else {
                continue;
            }
            $config['layers'][] = $layerConfig;
        }

        return [
            'map' => $map,
            'config' => $config,
        ];
    }
}
