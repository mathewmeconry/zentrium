<?php

namespace Zentrium\Bundle\CoreBundle\Menu;

class MenuListener
{
    public function onCreateMainMenuFirst(ConfigureMenuEvent $event)
    {
        $event->getMenu()->addChild('zentrium.menu.home', ['route' => 'home', 'labelAttributes' => ['icon' => 'fa fa-home']]);
    }

    public function onCreateMainMenuLast(ConfigureMenuEvent $event)
    {
        $event->getMenu()->addChild('zentrium.menu.users', ['route' => 'users', 'labelAttributes' => ['icon' => 'fa fa-users']]);
    }

    public function onCreateViewerMenu(ConfigureMenuEvent $event)
    {
        $event->getMenu()->addChild('zentrium.menu.viewer.user_profile', ['route' => 'viewer_user_profile']);
    }
}
