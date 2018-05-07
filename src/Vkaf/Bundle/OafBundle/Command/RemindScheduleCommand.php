<?php

namespace Vkaf\Bundle\OafBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Vkaf\Bundle\OafBundle\Entity\ShiftReminder;
use Vkaf\Bundle\OafBundle\Push\PushManager;
use Zentrium\Bundle\CoreBundle\Entity\User;

class RemindScheduleCommand extends Command
{
    private $em;
    private $pushManager;
    private $translator;
    private $urlGenerator;

    public function __construct(EntityManagerInterface $em, PushManager $pushManager, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct();
        $this->em = $em;
        $this->pushManager = $pushManager;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    protected function configure()
    {
        $this
            ->setName('vkaf:oaf:schedule:remind')
            ->setDescription('Send notifications about upcoming shifts.')
            ->addArgument(
                'window',
                InputArgument::REQUIRED,
                'Time up to which shifts are considered (in minutes).'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $window = intval($input->getArgument('window'));
        $limit = DateTime::createFromFormat('U', time() + $window * 60);

        $shifts = $this->em->getRepository(ShiftReminder::class)->findUpcoming($limit);

        foreach ($shifts as $shift) {
            $user = $this->em->find(User::class, $shift['user']);
            $reminder = new ShiftReminder($user, $shift['from']);
            $this->em->persist($reminder);
            $this->em->flush();

            $this->sendReminder($reminder);
        }
    }

    private function sendReminder(ShiftReminder $reminder)
    {
        $title = $this->translator->trans('vkaf_oaf.schedule.reminder.title', ['%from%' => $reminder->getFrom()->format('H:i')]);
        $url = $this->urlGenerator->generate('schedule_viewer_shifts', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->pushManager->send(
            $reminder->getUser(),
            'shift',
            $title,
            null,
            $url
        );
    }
}
