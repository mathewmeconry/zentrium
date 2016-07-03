<?php

namespace Vkaf\Bundle\OafBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vkaf\Bundle\OafBundle\Entity\Kiosk;

class ListKioskCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:kiosk:list')
            ->setDescription('Display all kiosk terminals.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['ID', 'Label', 'Token']);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $kiosks = $em->getRepository(Kiosk::class)->findAll();
        $rows = [];
        foreach ($kiosks as $kiosk) {
            $rows[] = [
                $kiosk->getId(),
                $kiosk->getLabel(),
                $kiosk->getToken(),
            ];
        }
        $table->setRows($rows);

        $table->render();
    }
}
