<?php

namespace Zentrium\Bundle\CoreBundle\Dashboard;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Templating\EngineInterface;
use Zentrium\Bundle\CoreBundle\Entity\TextWidgetManager;
use Zentrium\Bundle\CoreBundle\Entity\UserRepository;

class DashboardListener
{
    private $engine;
    private $userRepository;
    private $textWidgetManager;
    private $commonMarkConverter;

    public function __construct(EngineInterface $engine, UserRepository $userRepository, TextWidgetManager $textWidgetManager, CommonMarkConverter $commonMarkConverter)
    {
        $this->engine = $engine;
        $this->userRepository = $userRepository;
        $this->textWidgetManager = $textWidgetManager;
        $this->commonMarkConverter = $commonMarkConverter;
    }

    public function onBuildDashboard(BuildDashboardEvent $event)
    {
        $this->addUserCountWidget($event);
        $this->addTextWidgets($event);
        $this->addNewTextWidget($event);
    }

    private function addUserCountWidget(BuildDashboardEvent $event)
    {
        $userCount = $this->userRepository->count();
        $content = $this->engine->render(
            'ZentriumCoreBundle:Dashboard:userCountWidget.html.twig',
            ['count' => $userCount]
        );
        $event->addWidget(Position::TOP, $content, -50);
    }

    private function addTextWidgets(BuildDashboardEvent $event)
    {
        foreach ($this->textWidgetManager->findAll() as $widget) {
            $content = $this->engine->render('ZentriumCoreBundle:Dashboard:textWidget.html.twig', [
                'widget' => $widget,
                'content' => $this->commonMarkConverter->convertToHtml($widget->getContent()),
            ]);
            $event->addWidget($widget->getPosition(), $content, $widget->getPriority());
        }
    }

    private function addNewTextWidget(BuildDashboardEvent $event)
    {
        $content = $this->engine->render('ZentriumCoreBundle:Dashboard:textWidgetNew.html.twig');
        $event->addWidget(Position::CENTER, $content, -100);
    }
}
