<?php

namespace Vkaf\Bundle\OafBundle\Timesheet;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vkaf\Bundle\OafBundle\Entity\EntryApproval;
use Vkaf\Bundle\OafBundle\Entity\Terminal;
use Vkaf\Bundle\OafBundle\Pushpin\GripPublisher;
use Vkaf\Bundle\OafBundle\Terminal\FlowEvent;
use Vkaf\Bundle\OafBundle\Terminal\TerminalManager;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

class ApprovalHandler
{
    const FLOW_TAG = 'timesheet';

    private $grip;
    private $terminals;
    private $em;
    private $validator;
    private $translator;

    public function __construct(GripPublisher $grip, TerminalManager $terminals, EntityManagerInterface $em, ValidatorInterface $validator, TranslatorInterface $translator)
    {
        $this->grip = $grip;
        $this->terminals = $terminals;
        $this->em = $em;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    public function start(Entry $entry, Terminal $terminal, User $attester = null)
    {
        $time = sprintf('%s - %s', $entry->getStart()->format('d.m.Y H:i'), $entry->getEnd()->format('d.m.Y H:i'));
        $params = [
            [$this->translator->trans('vkaf_oaf.timesheet.approve.user'), $entry->getUser()->getName()],
            [$this->translator->trans('vkaf_oaf.timesheet.approve.time'), $time],
            [$this->translator->trans('zentrium_timesheet.entry.field.notes'), $entry->getNotes() ?? '-'],
        ];

        $channel = bin2hex(random_bytes(10));
        $context = [
            'entry' => $entry->getId(),
            'channel' => $channel,
            'attester' => $attester ? $attester->getId() : null,
        ];

        $this->terminals->start($terminal, 'signature', $params, self::FLOW_TAG, $context);

        return $channel;
    }

    public function onFlowEvent(FlowEvent $event)
    {
        if ($event->getTag() !== self::FLOW_TAG) {
            return;
        }
        $context = $event->getContext();

        if ($event->getMessage() === null) {
            $this->grip->publish($context['channel'], ['failure' => 'vkaf_oaf.timesheet.cancelled']);

            return;
        }

        $entry = $this->em->find(Entry::class, $context['entry']);
        if (!$entry || $entry->isApproved()) {
            $this->grip->publish($context['channel'], ['failure' => 'vkaf_oaf.timesheet.already_approved']);

            return;
        }

        $entry->setApprovedBy($entry->getUser());
        $entry->setApprovedAt(new DateTime());
        $entryApproval = new EntryApproval($entry);
        $entryApproval->setSignature($event->getMessage());
        if ($context['attester']) {
            $entryApproval->setAttester($this->em->find(User::class, $context['attester']));
        }

        if (count($this->validator->validate($entryApproval))) {
            $this->grip->publish($context['channel'], ['failure' => 'vkaf_oaf.timesheet.invalid_signature']);

            return;
        }

        $this->em->transactional(function (EntityManagerInterface $em) use ($entryApproval) {
            $em->persist($entryApproval);
        });

        $this->grip->publish($context['channel'], ['success' => null]);
    }
}
