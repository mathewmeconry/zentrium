<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/schedules")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("/", name="schedules")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('schedule_requirements');
    }
}
