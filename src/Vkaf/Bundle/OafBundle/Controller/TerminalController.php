<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vkaf\Bundle\OafBundle\Pushpin\GripRequest;

/**
 * @Route("/oaf/terminals")
 */
class TerminalController extends Controller
{
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
