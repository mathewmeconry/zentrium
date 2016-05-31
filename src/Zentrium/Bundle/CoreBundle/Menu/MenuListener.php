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
        $menu = $event->getMenu()->addChild('zentrium.menu.users', ['route' => 'users', 'labelAttributes' => ['icon' => 'fa fa-users']]);
        $menu->addChild('zentrium.menu.users', ['route' => 'users']);
        $menu->addChild('zentrium.menu.groups', ['route' => 'groups']);
    }

    public function onCreateViewerMenu(ConfigureMenuEvent $event)
    {
        $event->getMenu()->addChild('zentrium.menu.viewer.user_profile', ['route' => 'viewer_user_profile']);
    }
}
