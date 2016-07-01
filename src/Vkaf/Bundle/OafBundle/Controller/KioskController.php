<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/kiosk")
 */
class KioskController extends Controller
{
    /**
     * @Route("", name="kiosk")
     * @Template
     */
    public function welcomeAction()
    {
        return [];
    }
}
