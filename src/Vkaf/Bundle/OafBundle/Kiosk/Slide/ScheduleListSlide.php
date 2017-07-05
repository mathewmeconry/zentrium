<?php

namespace Vkaf\Bundle\OafBundle\Kiosk\Slide;

use League\Period\Period;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;
use Zentrium\Bundle\ScheduleBundle\Entity\ShiftManager;

class ScheduleListSlide implements SlideInterface
{
    const DEFAULT_COLUMNS = 2;
    const DEFAULT_HORIZON = 12;

    private $shiftManager;
    private $userRepository;

    public function __construct(ShiftManager $shiftManager, UserRepository $userRepository)
    {
        $this->shiftManager = $shiftManager;
        $this->userRepository = $userRepository;
    }

    public function render($options, $next)
    {
        $columns = intval($options['columns'] ?? self::DEFAULT_COLUMNS);
        $horizon = intval($options['horizon'] ?? self::DEFAULT_HORIZON);
        $period = Period::createFromDuration('- 2 hours', sprintf('%d hours', $horizon));

        $users = $this->userRepository->findAll();
        if (isset($options['offset'])) {
            $users = array_slice($users, intval($options['offset']));
        }
        if (isset($options['count'])) {
            $users = array_slice($users, 0, intval($options['count']));
        }
        $rows = [];
        foreach ($users as $user) {
            $rows[$user->getId()] = ['user' => $user, 'shifts' => []];
        }

        $shifts = $this->shiftManager->findPublishedInPeriod($period);
        foreach ($shifts as $shift) {
            $userId = $shift->getUser()->getId();
            if (isset($rows[$userId])) {
                $rows[$userId]['shifts'][] = $shift;
            }
        }

        return [
            'horizon' => $horizon,
            'columns' => array_chunk($rows, ceil(count($rows) / $columns)),
            'next' => $next,
        ];
    }
}
