<?php

namespace Zentrium\Bundle\CoreBundle\Security;

use DateTime;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManager
{
    private $userManager;
    private $tokenGenerator;
    private $urlGenerator;

    public function __construct(UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $urlGenerator)
    {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    public function invite(UserInterface $user)
    {
        if ($user->getUsername() === null) {
            return null;
        }

        if ($user->getConfirmationToken() === null) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $user->setPasswordRequestedAt(new DateTime());
        $this->userManager->updateUser($user);

        return $this->urlGenerator->generate('invitation', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
