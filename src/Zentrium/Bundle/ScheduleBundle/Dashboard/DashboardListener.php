<?php

namespace Zentrium\Bundle\ScheduleBundle\Dashboard;

use Symfony\Component\Templating\EngineInterface;
use Zentrium\Bundle\CoreBundle\Dashboard\BuildDashboardEvent;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;
use Zentrium\Bundle\ScheduleBundle\Entity\ShiftManager;

class DashboardListener
{
    private $templating;
    private $shiftManager;

    public function __construct(EngineInterface $templating, ShiftManager $shiftManager)
    {
        $this->templating = $templating;
        $this->shiftManager = $shiftManager;
    }

    public function onBuildDashboard(BuildDashboardEvent $event)
    {
        $content = $this->templating->render('ZentriumScheduleBundle:Dashboard:widget.html.twig', [
            'count' => $this->shiftManager->countPublished(),
        ]);
        $event->addWidget(Position::TOP, $content, 10);
    }
}
