<?php

namespace Vkaf\Bundle\OafBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vkaf\Bundle\OafBundle\Entity\EntryApproval;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

class RevertEntryApprovalCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:entry:revert-approval')
            ->setDescription('Revert an approval of an entry.')
            ->addArgument(
                'entry',
                InputArgument::REQUIRED,
                'ID of the entry'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $entry = $this->em->find(Entry::class, $input->getArgument('entry'));
        if (!$entry) {
            throw new InvalidArgumentException('Could not find entry.');
        }

        if (!$entry->getApprovedAt()) {
            $io->warning(sprintf('The entry is not approved yet.'));

            return;
        }

        $entry->setApprovedAt(null);
        $entry->setApprovedBy(null);
        $entryApproval = $this->em->find(EntryApproval::class, $entry->getId());
        $this->em->persist($entry);
        if ($entryApproval) {
            $this->em->remove($entryApproval);
        }
        $this->em->flush();

        $io->success(sprintf('Reverted the approval of the entry with ID %d.', $entry->getId()));
    }
}
