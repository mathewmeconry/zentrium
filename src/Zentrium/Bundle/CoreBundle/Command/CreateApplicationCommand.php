<?php

namespace Zentrium\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateApplicationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zentrium:application:create')
            ->setDescription('Create a new application')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $clientManager->updateClient($client);

        $output->writeln(sprintf('Application ID: <info>%s</info>', $client->getPublicId()));
        $output->writeln(sprintf('Application Secret: <info>%s</info>', $client->getSecret()));
    }
}
