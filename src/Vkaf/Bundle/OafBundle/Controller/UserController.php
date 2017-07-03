<?php

namespace Vkaf\Bundle\OafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vkaf\Bundle\OafBundle\Form\Type\UserDeskType;
use Zentrium\Bundle\CoreBundle\Entity\User;

/**
 * @Route("/oaf/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/dashboard", name="oaf_user_desk_dashboard")
     * @Template
     */
    public function deskDashboardAction(Request $request)
    {
        $form = $this->createForm(UserDeskType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('oaf_user_desk', ['user' => $form->getData()['user']->getId()]);
    }

    /**
     * @Route("/{user}", name="oaf_user_desk")
     * @Template
     */
    public function deskAction(User $user)
    {
        $timesheetHours = floor($this->get('zentrium_timesheet.manager.entry')->sumByUser($user) / 3600);

        return [
            'user' => $user,
            'timesheetHours' => $timesheetHours,
        ];
    }
}
