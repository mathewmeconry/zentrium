<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use FOS\UserBundle\Event\FormEvent;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Form\Type\UserAccountType;
use Zentrium\Bundle\CoreBundle\Form\Type\UserType;
use Zentrium\Bundle\CoreBundle\User\UserEvents;

class UserController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/users", name="users")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $users = $this->get('zentrium.repository.user')->findAllWithGroups();

        return ['users' => $users];
    }

    /**
     * @Route("/users/new", name="user_new")
     * @Secure("ROLE_ADMINISTRATOR")
     * @Template
     */
    public function newAction(Request $request)
    {
        $user = $this->get('fos_user.user_manager')->createUser();
        $user->setPlainPassword($this->get('fos_user.util.token_generator')->generateToken());

        return $this->handleEdit($request, $user);
    }

    /**
     * @Route("/users/{user}/edit", name="user_edit")
     * @Template
     */
    public function editAction(Request $request, User $user)
    {
        return $this->handleEdit($request, $user);
    }

    /**
     * @Route("/users/{user}/labels", name="user_labels")
     * @Secure("ROLE_MANAGER")
     * @Template
     */
    public function labelsAction(Request $request, User $user)
    {
        return [
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'groups' => $user->getGroups(),
        ];
    }

    private function handleEdit(Request $request, User $user)
    {
        $formClass = ($this->isGranted('ROLE_ADMINISTRATOR') ? UserAccountType::class : UserType::class);
        $form = $this->createForm($formClass, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(UserEvents::EDIT_SUCCESS, $event);

            $manager = $this->get('fos_user.user_manager');
            $manager->updateUser($user);

            $response = $event->getResponse();
            if ($response === null) {
                $response = $this->redirectToRoute('users');
                $this->addFlash('success', 'zentrium.user.form.saved');
            }

            return $response;
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }
}
