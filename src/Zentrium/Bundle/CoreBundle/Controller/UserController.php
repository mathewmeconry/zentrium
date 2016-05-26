<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Form\Type\UserType;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $users = $this->get('zentrium.repository.user')->findAll();

        return ['users' => $users];
    }

    /**
     * @Route("/users/{user}/edit", name="user_edit")
     * @Template
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
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
