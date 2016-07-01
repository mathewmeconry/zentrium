<?php

namespace Vkaf\Bundle\OafBundle\Command;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vkaf\Bundle\OafBundle\Entity\Kiosk;

class RemoveKioskCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:kiosk:remove')
            ->setDescription('Remove a kiosk terminal.')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'Token of the terminal.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $kiosk = $em->getRepository(Kiosk::class)->findOneByToken($token);
        if ($kiosk === null) {
            throw new InvalidArgumentException('Could not find kiosk terminal.');
        }

        $em->remove($kiosk);
        $em->flush();
    }
}
