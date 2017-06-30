<?php

namespace Zentrium\Bundle\LogBundle\Dashboard;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\EngineInterface;
use Zentrium\Bundle\CoreBundle\Dashboard\BuildDashboardEvent;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;
use Zentrium\Bundle\LogBundle\Entity\Log;
use Zentrium\Bundle\LogBundle\Entity\LogRepository;

class DashboardListener
{
    private $engine;
    private $logRepository;
    private $authorizationChecker;

    public function __construct(EngineInterface $engine, LogRepository $logRepository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->engine = $engine;
        $this->logRepository = $logRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onBuildDashboard(BuildDashboardEvent $event)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_LOG_READ')) {
            return;
        }

        $stats = array_merge([Log::STATUS_OPEN => 0], $this->logRepository->aggregateByStatus());
        $logWidget = $this->engine->render(
            'ZentriumLogBundle:Dashboard:logWidget.html.twig',
            ['count' => $stats[Log::STATUS_OPEN]]
        );
        $event->addWidget(Position::TOP, $logWidget);
    }
}
