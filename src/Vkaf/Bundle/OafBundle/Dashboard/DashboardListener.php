<?php

namespace Vkaf\Bundle\OafBundle\Dashboard;

use DateInterval;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Templating\EngineInterface;
use Vkaf\Bundle\OafBundle\Entity\ShiftRepository;
use Vkaf\Bundle\OafBundle\Form\Type\UserDeskType;
use Vkaf\Bundle\OafBundle\Lineup\LineupManager;
use Zentrium\Bundle\CoreBundle\Dashboard\BuildDashboardEvent;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;
use Zentrium\Bundle\ScheduleBundle\Entity\ScheduleManager;

class DashboardListener
{
    private $templating;
    private $lineupManager;
    private $scheduleManager;
    private $shiftRepository;
    private $formFactory;

    public function __construct(EngineInterface $templating, LineupManager $lineupManager, ScheduleManager $scheduleManager, ShiftRepository $shiftRepository, FormFactoryInterface $formFactory)
    {
        $this->templating = $templating;
        $this->lineupManager = $lineupManager;
        $this->scheduleManager = $scheduleManager;
        $this->shiftRepository = $shiftRepository;
        $this->formFactory = $formFactory;
    }

    public function onBuildDashboard(BuildDashboardEvent $event)
    {
        if (($lineup = $this->lineupManager->get()) !== null) {
            $lineupWidget = $this->templating->render(
                'VkafOafBundle:Dashboard:lineup.html.twig',
                ['days' => $this->groupByDay($lineup)]
            );
            $event->addWidget(Position::TOP, $lineupWidget);
        }

        $schedule = $this->findSingleSchedule();
        $scheduleCount = $this->shiftRepository->countActive(new DateTime());
        $scheduleWidget = $this->templating->render(
            'VkafOafBundle:Dashboard:schedule.html.twig',
            ['count' => $scheduleCount, 'schedule' => $schedule]
        );
        $event->addWidget(Position::TOP, $scheduleWidget, 20);

        $userDeskForm = $this->formFactory->create(UserDeskType::class);
        $userDeskWidget = $this->templating->render(
            'VkafOafBundle:Dashboard:userDesk.html.twig',
            ['form' => $userDeskForm->createView()]
        );
        $event->addWidget(Position::SIDEBAR, $userDeskWidget);
    }

    private function groupByDay(array $lineup)
    {
        $dayInterval = new DateInterval('P1D');
        $days = [];
        foreach ($lineup as $row) {
            $day = $row['begin'];
            if ($day->format('H') < 6) {
                $day = clone $day;
                $day->sub($dayInterval);
            }

            $dayKey = $day->format('Y-m-d');
            if (!isset($days[$dayKey])) {
                $days[$dayKey] = [];
            }

            $days[$dayKey][] = $row;
        }

        return $days;
    }

    private function findSingleSchedule()
    {
        $now = new DateTime();
        $active = [];
        $published = [];
        foreach ($this->scheduleManager->findAll() as $schedule) {
            if ($schedule->isPublished()) {
                $published[] = $schedule;
                if ($schedule->getBegin() <= $now && $schedule->getEnd() > $now) {
                    $active[] = $schedule;
                }
            }
        }

        if (count($active) === 1) {
            return $active[0];
        } elseif (count($published) === 1) {
            return $published[0];
        } else {
            return null;
        }
    }
}
