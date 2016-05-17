<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/viewer")
 */
class ViewerController extends Controller
{
    /**
     * @Route("/", name="viewer")
     */
    public function indexAction()
    {
        $firstMenuEntry = $this->get('knp_menu.menu_provider')->get('viewer')->getFirstChild();

        return $this->redirect($firstMenuEntry->getUri());
    }

    /**
     * @Route("/profile", name="viewer_user_profile")
     * @Template
     */
    public function userProfileAction()
    {
        return [
            'user' => $this->getUser(),
        ];
    }
}
