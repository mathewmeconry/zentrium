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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;

class SecurityController extends Controller
{
    /**
     * @Route("/password/request", name="password_request")
     * @Template
     */
    public function requestPasswordAction(Request $request)
    {
        $username = $request->request->get('username');
        if ($username === null) {
            return [];
        }

        $token = $request->request->get('token');
        if ($token === null || !$this->get('security.csrf.token_manager')->isTokenValid(new CsrfToken('password_reset', $token))) {
            throw new BadRequestHttpException();
        }

        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
        if ($user === null) {
            return [
                'error' => 'zentrium.security.password_reset.invalid_username',
            ];
        }

        if ($user->getUsername() === null || $user->getEmail() === null) {
            return [
                'error' => 'zentrium.security.password_reset.incomplete_profile',
            ];
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return [
                'error' => 'zentrium.security.password_reset.already_requested',
            ];
        }

        if ($user->getConfirmationToken() === null) {
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return $this->redirectToRoute('password_request_sent');
    }

    /**
     * @Route("/password/request/sent", name="password_request_sent")
     * @Template
     */
    public function requestSentAction()
    {
        return [];
    }

    /**
     * @Route("/password/reset/{token}", name="fos_user_resetting_reset")
     * @Template
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $formFactory = $this->get('fos_user.resetting.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === ($response = $event->getResponse())) {
                $response = $this->redirectToRoute('home');
            }

            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return [
            'token' => $token,
            'form' => $form->createView(),
        ];
    }
}
