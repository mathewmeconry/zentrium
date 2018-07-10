<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Pushpin\GripRequest;
use Vkaf\Bundle\OafBundle\Pushpin\GripResponse;
use Vkaf\Bundle\OafBundle\Pushpin\MessageEvent;
use Vkaf\Bundle\OafBundle\Pushpin\OpenEvent;
use Vkaf\Bundle\OafBundle\Pushpin\SubscribeEvent;
use Zentrium\Bundle\TimesheetBundle\Entity\Entry;

/**
 * @Route("/oaf/timesheet")
 */
class TimesheetController extends Controller
{
    /**
     * @Route("/{entry}/approve", name="oaf_timesheet_approve")
     * @Template
     */
    public function approveAction(Request $request, Entry $entry)
    {
        $userId = $entry->getUser()->getId();
        if ($scheduleId = $request->query->get('schedule')) {
            $returnUrl = $this->generateUrl('oaf_schedule_user', ['user' => $userId, 'schedule' => $scheduleId]);
        } else {
            $returnUrl = $this->generateUrl('oaf_user_desk', ['user' => $userId]);
        }

        return [
            'entry' => $entry,
            'return' => $returnUrl,
        ];
    }

    /**
     * @Route("/{entry}/approve/control", name="oaf_timesheet_approve_control")
     * @Method("POST")
     */
    public function approveControlAction(Request $httpRequest, GripRequest $request, Entry $entry)
    {
        $response = new GripResponse();
        foreach ($request->getEvents() as $event) {
            if ($event instanceof OpenEvent) {
                $response->addEvent(new OpenEvent());
                if ($entry->isApproved()) {
                    $response->addEvent(new MessageEvent(['failure' => null]));
                } else {
                    $terminals = $this->get('vkaf_oaf.terminals');
                    $response->addEvent(new SubscribeEvent($terminals->getStatusChannel()));
                    $response->addEvent(new MessageEvent($terminals->renderStatus()));
                }
            } elseif ($event instanceof MessageEvent) {
                $message = $event->getMessage();
                if (isset($message['terminal']) && is_int($message['terminal'])) {
                    $terminal = $this->get('vkaf_oaf.repository.terminal')->find($message['terminal']);
                    if (!$terminal) {
                        continue;
                    }
                    $channel = $this->get('vkaf_oaf.timesheet.approvals')->start($entry, $terminal, $this->getUser());
                    $response->addEvent(new SubscribeEvent($channel));
                }
            }
        }

        return $response;
    }
}
