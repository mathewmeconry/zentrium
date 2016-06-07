<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use FOS\UserBundle\Event\FormEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\User;
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

    private function handleEdit(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $dispatcher = $this->get('event_dispatcher');
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(UserEvents::EDIT_SUCCESS, new FormEvent($form, $request));

            $manager = $this->get('fos_user.user_manager');
            $manager->updateUser($user);

            $this->addFlash('success', 'zentrium.user.form.saved');

            return $this->redirectToRoute('users');
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }
}
