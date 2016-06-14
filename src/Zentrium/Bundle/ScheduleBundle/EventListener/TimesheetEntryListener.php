<?php

namespace Zentrium\Bundle\ScheduleBundle\EventListener;

use Zentrium\Bundle\ScheduleBundle\Entity\Shift;
use Zentrium\Bundle\ScheduleBundle\Entity\ShiftManager;
use Zentrium\Bundle\TimesheetBundle\Event\GetResponseEntryEvent;

class TimesheetEntryListener
{
    private $shiftManager;

    public function __construct(ShiftManager $shiftManager)
    {
        $this->shiftManager = $shiftManager;
    }

    public function onEditInitialize(GetResponseEntryEvent $event)
    {
        $shiftId = intval($event->getRequest()->query->get('shift'));
        if ($shiftId <= 0) {
            return;
        }

        $shift = $this->shiftManager->find($shiftId);
        if ($shift === null) {
            return;
        }

        $entry = $event->getEntry();
        $entry->setUser($shift->getUser());
        $entry->setStart($shift->getFrom());
        $entry->setEnd($shift->getTo());
        $entry->setNotes($this->buildNotes($shift));
    }

    private function buildNotes(Shift $shift)
    {
        return sprintf('%s (%s)', $shift->getTask()->getName(), $shift->getTask()->getCode());
    }
}
