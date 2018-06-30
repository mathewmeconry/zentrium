<?php

namespace Vkaf\Bundle\OafBundle\Timesheet;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Zentrium\Bundle\TimesheetBundle\Event\GetResponseEntryEvent;

class EntryListener
{
    private $urlGenerator;
    private $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function onEditCompleted(GetResponseEntryEvent $event)
    {
        if ($event->getResponse() !== null) {
            return;
        }

        $scheduleId = $event->getRequest()->query->get('return_oaf_schedule');
        if (!is_string($scheduleId)) {
            return;
        }

        $scheduleUrl = $this->urlGenerator->generate('oaf_timesheet_approve', [
            'entry' => $event->getEntry()->getId(),
            'schedule' => $scheduleId,
        ]);
        $event->setResponse(new RedirectResponse($scheduleUrl));
    }
}
