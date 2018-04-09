<?php

namespace Vkaf\Bundle\OafBundle\Command;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportLineupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:lineup:import')
            ->setDescription('Import line-up from a file.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to the file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('file');
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('"%s" is not readable.', $path));
        }

        $data = json_decode(file_get_contents($input->getArgument('file')), true);
        if (!is_array($data)) {
            throw new RuntimeException('Could not parse JSON.');
        }

        $manager = $this->getContainer()->get('vkaf_oaf.lineup');
        $manager->import($data);
    }
}
