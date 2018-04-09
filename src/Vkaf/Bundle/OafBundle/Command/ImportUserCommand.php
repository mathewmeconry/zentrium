<?php

namespace Vkaf\Bundle\OafBundle\Command;

use DateTime;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zentrium\Bundle\CoreBundle\Entity\Group;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\ScheduleBundle\Entity\Skill;
use Zentrium\Bundle\ScheduleBundle\Entity\User as ScheduleUser;

class ImportUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:user:import')
            ->setDescription('Import users from a file.')
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

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $scheduleUserManager = $this->getContainer()->get('zentrium_schedule.manager.user');

        $groups = $em->getRepository(Group::class)->findAll();
        $groupMap = [];
        foreach ($groups as $group) {
            $groupMap[$group->getName()] = $group;
            $groupMap[$group->getShortName()] = $group;
        }

        $skills = $em->getRepository(Skill::class)->findAll();
        $skillMap = [];
        foreach ($skills as $skill) {
            $skillMap[$skill->getName()] = $skill;
            $skillMap[$skill->getShortName()] = $skill;
        }

        $genderMap = [
            'Mann' => 'male',
            'Frau' => 'female',
        ];

        $fh = fopen($path, 'r');
        $header = null;
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

            $username = strtolower($mapped['firstname'].'.'.$mapped['lastname']);
            $username = str_replace(' ', '.', $username);
            $username = iconv('UTF8', 'ASCII//TRANSLIT', $username);

            $groups = preg_split('/[ ,;\/]+/', $mapped['groups']);
            $skills = preg_split('/[ ,;\/]+/', $mapped['skills']);
            $skills = array_diff($skills, ['und', 'oder']);
            if (in_array('Verkehrskadett', $skills)) {
                $skills = array_diff($skills, ['Verkehrskadett']);
                if (!in_array('VK-PL', $groups)) {
                    $skills[] = 'VD';
                }
                $skills[] = 'PD';
            }

            $user = new User();
            $user->setUsername($username);
            $user->setFirstName($mapped['firstname']);
            $user->setLastName($mapped['lastname']);
            $user->setEmail($mapped['email']);
            $user->setPlainPassword(base64_encode(openssl_random_pseudo_bytes(20)));
            $user->setTitle($mapped['title']);
            foreach ($groups as $group) {
                if (!isset($groupMap[$group])) {
                    throw new RuntimeException();
                }
                $user->getGroups()->add($groupMap[$group]);
            }
            if ($mapped['birthday']) {
                $user->setBirthday(new DateTime($mapped['birthday']));
            }
            if ($mapped['gender']) {
                if (preg_match('/^m/i', $mapped['gender'])) {
                    $user->setGender('male');
                } elseif (preg_match('/^[wf]/i', $mapped['gender'])) {
                    $user->setGender('female');
                } else {
                    throw new RuntimeException();
                }
            }

            $userManager->updateUser($user);

            $scheduleUser = new ScheduleUser($user);
            foreach ($skills as $skill) {
                if (!isset($skillMap[$skill])) {
                    throw new RuntimeException();
                }
                $scheduleUser->getSkills()->add($skillMap[$skill]);
            }
            $scheduleUser->setNotes($mapped['notes']);

            $scheduleUserManager->save($scheduleUser);
        }
        fclose($fh);
    }
}
