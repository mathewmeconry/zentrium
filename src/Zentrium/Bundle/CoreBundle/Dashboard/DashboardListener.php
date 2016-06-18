<?php

namespace Zentrium\Bundle\CoreBundle\Dashboard;

use Symfony\Component\Templating\EngineInterface;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class DashboardListener
{
    private $engine;
    private $userRepository;

    public function __construct(EngineInterface $engine, UserRepository $userRepository)
    {
        $this->engine = $engine;
        $this->userRepository = $userRepository;
    }

    public function onBuildDashboard(BuildDashboardEvent $event)
    {
        $userCount = $this->userRepository->count();
        $content = $this->engine->render(
            'ZentriumCoreBundle:Dashboard:userCountWidget.html.twig',
            ['count' => $userCount]
        );
        $event->addWidget(Position::TOP, $content, -50);
    }
}
