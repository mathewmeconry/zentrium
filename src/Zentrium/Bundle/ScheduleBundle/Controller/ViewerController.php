<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/viewer/schedule")
 */
class ViewerController extends Controller
{
    /**
     * @Route("/shifts", name="schedule_viewer_shifts")
     * @Template
     */
    public function shiftsAction()
    {
        $user = $this->getUser();
        $entries = [];
        $now = new DateTime();

        $shifts = $this->get('zentrium_schedule.manager.shift')->findUpcomingByUser($user);
        foreach ($shifts as $shift) {
            $entries[] = [
                'type' => 'shift',
                'time' => $shift->getFrom(),
                'shift' => $shift,
            ];
        }

        $availabilities = $this->get('zentrium_schedule.manager.availability')->findUpcomingByUser($user);
        foreach ($availabilities as $availability) {
            if ($availability->getFrom() >= $now) {
                $entries[] = [
                    'type' => 'available',
                    'time' => $availability->getFrom(),
                ];
            }
            $entries[] = [
                'type' => 'unavailable',
                'time' => $availability->getTo(),
            ];
        }

        uasort($entries, function ($a, $b) {
            return $a['time']->getTimestamp() - $b['time']->getTimestamp();
        });

        return [
            'user' => $user,
            'entries' => $entries,
        ];
    }
}
