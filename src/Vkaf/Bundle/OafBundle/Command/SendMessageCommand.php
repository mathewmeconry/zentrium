<?php

namespace Vkaf\Bundle\OafBundle\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vkaf\Bundle\OafBundle\Announcement\MessengerInterface;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class SendMessageCommand extends Command
{
    private $messenger;
    private $users;

    public function __construct(MessengerInterface $messenger, UserRepository $users)
    {
        parent::__construct();

        $this->messenger = $messenger;
        $this->users = $users;
    }

    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:message:send')
            ->setDescription('Send a message.')
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'Body of the message'
            )
            ->addArgument(
                'receiver',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'ID of the receiver'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $receivers = [];
        foreach ($input->getArgument('receiver') as $receiverId) {
            $receiver = $this->users->find($receiverId);
            if (!$receiver) {
                throw new InvalidArgumentException(sprintf('Could not find user with ID %d.', $receiverId));
            }
            $receivers[] = $receiver;
        }
        if (!$io->confirm(sprintf('Do you want to send a message to %d users?', count($receivers)), false)) {
            return;
        }

        $messageText = $input->getArgument('message');
        $message = $this->messenger->send($receivers, $messageText);

        $io->success(sprintf('Sent a message to %d users.', count($message->getDeliveries())));
    }
}
