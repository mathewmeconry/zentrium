<?php

namespace Zentrium\Bundle\MapBundle\Command;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zentrium\Bundle\MapBundle\Entity\WmtsLayer;

class UpdateWmtsLayerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zentrium:map:wmts')
            ->setDescription('Update a WMTS layer')
            ->addOption(
               'title',
               null,
               InputOption::VALUE_REQUIRED,
               'If set, the layer name will be set to this value.'
            )
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'GetCapabilities URL'
            )
            ->addArgument(
                'layer',
                InputArgument::OPTIONAL,
                'Layer identifier'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $layerId = $input->getArgument('layer');
        $title = $input->getOption('title');

        $manager = $this->getContainer()->get('zentrium_map.manager.wmts_layer');
        $parser = $this->getContainer()->get('zentrium_map.wmts.capabilities');

        $capabilities = $parser->parseFromUrl($url);

        $layerIds = [];
        $layerNames = [];
        $layerCache = [];
        $serviceTitle = $title !== null ? $title : $capabilities['ServiceIdentification']['Title'];
        foreach ($capabilities['Contents']['Layer'] as $layer) {
            $layerIds[] = $layer['Identifier'];
            $layerNames[$layer['Identifier']] = sprintf('%s (%s)', $layer['Title'], $serviceTitle);
        }

        if ($layerId !== null) {
            if (!in_array($layerId, $layerIds)) {
                throw new InvalidArgumentException('Unknown layer.');
            }
            $layerIds = [$layerId];
            if ($title !== null) {
                $layerNames[$layerId] = $title;
            }
        } else {
            $existingLayers = $manager->findByCapabilitiesUrl($url);
            foreach ($existingLayers as $layer) {
                if (!in_array($layer->getLayerId(), $layerIds)) {
                    $manager->delete($layer);
                } else {
                    $layerCache[$layer->getLayerId()] = $layer;
                }
            }
        }

        foreach ($layerIds as $layerId) {
            if (isset($layerCache[$layerId])) {
                $layer = $layerCache[$layerId];
            } else {
                $layer = new WmtsLayer();
            }
            $layer->setName($layerNames[$layerId]);
            $layer->setCapabilitiesUrl($url);
            $layer->setLayerId($layerId);
            $layer->setCapabilities(json_encode($parser->minify($capabilities, [$layerId])));
            $manager->upsert($layer, false);
        }
    }
}
