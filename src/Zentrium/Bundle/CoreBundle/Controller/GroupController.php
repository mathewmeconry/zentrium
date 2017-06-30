<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseGroupEvent;
use FOS\UserBundle\FOSUserEvents;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\Group;

/**
 * @Route("/groups")
 */
class GroupController extends Controller
{
    /**
     * @Route("/", name="groups")
     * @Template
     */
    public function indexAction()
    {
        $groups = $this->get('fos_user.group_manager')->findGroups();

        return [
            'groups' => $groups,
        ];
    }

    /**
     * @Route("/new", name="group_new")
     * @Secure("ROLE_ADMINISTRATOR")
     * @Template
     */
    public function newAction(Request $request)
    {
        $groupManager = $this->get('fos_user.group_manager');
        $group = $groupManager->createGroup('');

        return $this->handleEdit($request, $group);
    }

    /**
     * @Route("/{group}/edit", name="group_edit")
     * @Secure("ROLE_ADMINISTRATOR")
     * @Template
     */
    public function editAction(Request $request, Group $group)
    {
        return $this->handleEdit($request, $group);
    }

    private function handleEdit(Request $request, Group $group)
    {
        $new = ($group->getId() === null);

        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseGroupEvent($group, $request);
        $dispatcher->dispatch($new ? FOSUserEvents::GROUP_CREATE_INITIALIZE : FOSUserEvents::GROUP_EDIT_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->get('fos_user.group.form.factory');
        $form = $formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupManager = $this->get('fos_user.group_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch($new ? FOSUserEvents::GROUP_CREATE_SUCCESS : FOSUserEvents::GROUP_EDIT_SUCCESS, $event);

            $groupManager->updateGroup($group);

            if (null === $response = $event->getResponse()) {
                $response = $this->redirectToRoute('groups');
            }

            $event = new FilterGroupResponseEvent($group, $request, $response);
            $dispatcher->dispatch($new ? FOSUserEvents::GROUP_CREATE_COMPLETED : FOSUserEvents::GROUP_EDIT_COMPLETED, $event);

            return $response;
        }

        return [
            'group' => $group,
            'form' => $form->createView(),
        ];
    }
}
