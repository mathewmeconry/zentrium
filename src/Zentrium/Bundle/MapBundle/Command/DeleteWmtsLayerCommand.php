<?php

namespace Zentrium\Bundle\MapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteWmtsLayerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zentrium:map:delete-wmts')
            ->setDescription('Delete a WMTS layer')
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

        $manager = $this->getContainer()->get('zentrium_map.manager.wmts_layer');

        if ($layerId !== null) {
            $layer = $manager->findOneByCapabilitiesUrlAndLayerId($url, $layerId);
            if ($layer === null) {
                throw InvalidArgumentException('Could not find layer.');
            }
            $layers = [$layer];
        } else {
            $layers = $manager->findByCapabilitiesUrl($url);
        }

        $manager->deleteMultiple($layers);
    }
}
