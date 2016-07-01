<?php

namespace Vkaf\Bundle\OafBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vkaf\Bundle\OafBundle\Entity\Kiosk;

class CreateKioskCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:kiosk:create')
            ->setDescription('Create a new kiosk terminal.')
            ->addArgument(
                'label',
                InputArgument::REQUIRED,
                'Label for the terminal.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $label = $input->getArgument('label');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $kiosk = new Kiosk();
        $kiosk->setLabel($label);
        $kiosk->setToken($this->createToken());
        $em->persist($kiosk);
        $em->flush();

        $output->writeln(sprintf('Token: <info>%s</info>', $kiosk->getToken()));
    }

    private function createToken()
    {
        return bin2hex(random_bytes(16));
    }
}
