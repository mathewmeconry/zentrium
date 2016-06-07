<?php

namespace Zentrium\Bundle\MapBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\MapBundle\Entity\FeatureLayer;
use Zentrium\Bundle\MapBundle\Entity\Layer;
use Zentrium\Bundle\MapBundle\Entity\Map;
use Zentrium\Bundle\MapBundle\Entity\WmtsLayer;
use Zentrium\Bundle\MapBundle\Form\Type\MapType;
use Zentrium\Bundle\MapBundle\Form\Type\MapViewportType;

/**
 * @Route("/maps")
 */
class MapController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="maps")
     */
    public function indexAction()
    {
        $map = $this->get('zentrium_map.manager.map')->findDefault();

        return $this->redirectToRoute('map_view', ['map' => $map->getId()]);
    }

    /**
     * @Route("/new", name="map_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Map());
    }

    /**
     * @Route("/{map}/edit", name="map_edit")
     * @ParamConverter("map", options={"repository_method" = "findWithLayers"})
     * @Template
     */
    public function editAction(Request $request, Map $map)
    {
        return $this->handleEdit($request, $map);
    }

    /**
     * @Route("/{map}/default", name="map_set_default", options={"protect": true})
     * @Method("POST")
     */
    public function setDefaultAction(Request $request, Map $map)
    {
        $manager = $this->get('zentrium_map.manager.map');
        $manager->setDefaultMap($map);

        return new JsonResponse([
            'message' => $this->get('translator')->trans('zentrium_map.map.form.set_default'),
        ]);
    }

    /**
     * @Route("/{map}/viewport", name="map_viewport", options={"protect": true})
     * @Method("POST")
     */
    public function viewportAction(Request $request, Map $map)
    {
        $form = $this->createForm(MapViewportType::class, $map);

        $form->submit($request->request->get($form->getName()));

        if (!$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $this->get('zentrium_map.manager.map')->save($map);

        return new JsonResponse([
            'message' => $this->get('translator')->trans('zentrium_map.map.form.saved'),
        ]);
    }

    /**
     * @Route("/{map}", name="map_view")
     * @ParamConverter("map", options={"repository_method" = "findWithLayers"})
     * @Template
     */
    public function viewAction(Map $map)
    {
        $config = [
            'layers' => [],
            'center' => $map->getCenter(),
            'zoom' => $map->getZoom(),
            'projection' => $map->getProjection(),
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

    private function handleEdit(Request $request, Map $map)
    {
        $layerRepository = $this->getDoctrine()->getRepository(Layer::class);

        $form = $this->createForm(MapType::class, $map, [
            'layer_repository' => $layerRepository,
            'map_layers' => $map->getLayers(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('zentrium_map.manager.map')->save($map);

            $this->addFlash('success', 'zentrium_map.map.form.saved');

            return $this->redirectToRoute('map_view', ['map' => $map->getId()]);
        }

        return [
            'map' => $map,
            'form' => $form->createView(),
            'layers' => $layerRepository->findAll(),
        ];
    }
}
