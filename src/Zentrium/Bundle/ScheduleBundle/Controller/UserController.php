<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\User;

/**
 * @Route("/schedules/users")
 */
class UserController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="schedule_users")
     * @Template
     */
    public function indexAction()
    {
        $users = $this->get('zentrium_schedule.manager.user')->findAll();

        return [
            'users' => $users,
        ];
    }
}
