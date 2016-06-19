<?php

namespace Vkaf\Bundle\OafBundle\Command;

use DateTime;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Availability;

class ImportAvailabilityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:availability:import')
            ->setDescription('Import availability data from a file.')
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
            throw new RuntimeException(sprintf('"%" is not readable.', $path));
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $scheduleUserManager = $this->getContainer()->get('zentrium_schedule.manager.user');

        $fh = fopen($path, 'r');
        $header = null;
        $rows = [];
        while (($row = fgetcsv($fh)) !== false) {
            if ($header === null) {
                $header = $row;
                continue;
            }
            $mapped = [];
            foreach ($row as $i => $value) {
                if ($value === '') {
                    $value = null;
                }
                $mapped[$header[$i]] = $value;
            }

            $id = intval($mapped['user_id']);
            $baseUser = $userManager->findUserBy(['id' => $id]);
            if ($baseUser === null) {
                throw new RuntimeException(sprintf('Unknown user with ID %d.', $id));
            }
            $user = $scheduleUserManager->findOneByBase($baseUser);

            $availability = new Availability();
            $availability->setFrom(new DateTime($mapped['from']));
            $availability->setTo(new DateTime($mapped['to']));
            $availability->setUser($user);
            $user->getAvailabilities()->add($availability);

            $rows[] = [
                $availability->getId(),
                $baseUser->getId(),
                $baseUser->getName(true),
                $availability->getFrom()->format('Y-m-d H:i:s'),
                $availability->getTo()->format('Y-m-d H:i:s'),
            ];

            $scheduleUserManager->save($user);
        }
        fclose($fh);

        $table = new Table($output);
        $table->setHeaders(['ID', 'User ID', 'User', 'From', 'To']);
        $table->setRows($rows);
        $table->render();
    }
}
