<?php

namespace Vkaf\Bundle\OafBundle\Dashboard;

use DateInterval;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Templating\EngineInterface;
use Vkaf\Bundle\OafBundle\Form\Type\UserDeskType;
use Vkaf\Bundle\OafBundle\Lineup\LineupManager;
use Zentrium\Bundle\CoreBundle\Dashboard\BuildDashboardEvent;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;

class DashboardListener
{
    private $templating;
    private $lineupManager;
    private $formFactory;

    public function __construct(EngineInterface $templating, LineupManager $lineupManager, FormFactoryInterface $formFactory)
    {
        $this->templating = $templating;
        $this->lineupManager = $lineupManager;
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
}
