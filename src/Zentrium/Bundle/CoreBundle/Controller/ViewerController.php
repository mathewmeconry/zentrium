<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/password", name="viewer_change_password")
     * @Template
     */
    public function changePasswordAction(Request $request)
    {
        $dispatcher = $this->get('event_dispatcher');
        $user = $this->getUser();

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->get('fos_user.change_password.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            if (null === ($response = $event->getResponse())) {
                $response = $this->redirectToRoute('viewer_user_profile');
            }
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
