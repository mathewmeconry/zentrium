<?php

namespace Vkaf\Bundle\OafBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Templating\EngineInterface;
use Vkaf\Bundle\OafBundle\Entity\Kiosk;

class KioskAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $templating;

    public function __construct(EntityManager $em, EngineInterface $templating)
    {
        $this->em = $em;
        $this->templating = $templating;
    }

    public function getCredentials(Request $request)
    {
        return $request->query->get('token', '');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->em->getRepository(Kiosk::class)->findOneByToken($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($this->templating->render('VkafOafBundle:Kiosk:unauthorized.html.twig'), 401);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
