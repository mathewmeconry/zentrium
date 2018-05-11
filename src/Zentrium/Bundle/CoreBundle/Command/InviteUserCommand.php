<?php

namespace Zentrium\Bundle\CoreBundle\Command;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InviteUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zentrium:user:invite')
            ->setDescription('Invite a user')
            ->addArgument('username', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $user = $this->getContainer()->get('fos_user.user_manager')->findUserByUsername($username);
        if (!$user) {
            throw new InvalidArgumentException(sprintf('Could not find "%s".', $username));
        }

        $url = $this->getContainer()->get('zentrium.security.invitation')->invite($user);
        if (!$url) {
            throw new RuntimeException('Could not invite user.');
        }

        $output->writeln($url);
    }
}
