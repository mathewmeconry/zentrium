<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vkaf\Bundle\OafBundle\Entity\Terminal;
use Vkaf\Bundle\OafBundle\Pushpin\GripRequest;
use Vkaf\Bundle\OafBundle\Pushpin\GripResponse;
use Vkaf\Bundle\OafBundle\Pushpin\MessageEvent;
use Vkaf\Bundle\OafBundle\Pushpin\OpenEvent;
use Vkaf\Bundle\OafBundle\Pushpin\SubscribeEvent;

/**
 * @Route("/oaf/terminals")
 */
class TerminalController extends Controller
{
    /**
     * @Route("/", name="oaf_terminals")
     * @Template
     */
    public function listAction()
    {
        $terminals = $this->get('vkaf_oaf.repository.terminal')->findAll();

        return [
            'terminals' => $terminals,
        ];
    }

    /**
     * @Route("/status", name="oaf_terminal_status")
     * @Method("POST")
     */
    public function statusAction(GripRequest $request)
    {
        $response = new GripResponse();
        foreach ($request->getEvents() as $event) {
            if ($event instanceof OpenEvent) {
                $terminals = $this->get('vkaf_oaf.terminals');
                $response->addEvent(new OpenEvent());
                $response->addEvent(new SubscribeEvent($terminals->getStatusChannel()));
                $response->addEvent(new MessageEvent($terminals->renderStatus()));
            }
        }

        return $response;
    }

    /**
     * @Route("/app", name="oaf_terminal_app")
     * @Template
     */
    public function appAction()
    {
        return [
            'terminal' => $this->getUser(),
        ];
    }

    /**
     * @Route("/control", name="oaf_terminal_control")
     * @Method("POST")
     */
    public function controlAction(GripRequest $request)
    {
        return $this->get('vkaf_oaf.terminals')->handle($this->getUser(), $request);
    }
}
