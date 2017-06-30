<?php

namespace Zentrium\Bundle\LogBundle\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_LOG_READ')) {
            return;
        }

        $menu = $event->getMenu()->addChild('zentrium_log.menu.log', [
            'route' => 'logs',
            'labelAttributes' => ['icon' => 'fa fa-book'],
        ])->setExtra('routes', [['pattern' => '/^log(s|_.*)$/']]);
    }
}
