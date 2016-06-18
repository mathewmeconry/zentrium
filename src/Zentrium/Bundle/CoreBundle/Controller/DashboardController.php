<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Dashboard\BuildDashboardEvent;
use Zentrium\Bundle\CoreBundle\Dashboard\DashboardEvents;
use Zentrium\Bundle\CoreBundle\Dashboard\Position;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template
     */
    public function dashboardAction(Request $request)
    {
        if (!$this->isGranted('ROLE_MANAGER')) {
            return $this->redirectToRoute('viewer');
        }

        $event = new BuildDashboardEvent();
        $this->get('event_dispatcher')->dispatch(DashboardEvents::BUILD_DASHBOARD, $event);

        return [
            'widgetsTop' => $event->getWidgets(Position::TOP),
            'widgetsCenter' => $event->getWidgets(Position::CENTER),
            'widgetsSidebar' => $event->getWidgets(Position::SIDEBAR),
        ];
    }
}
