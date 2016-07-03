<?php

namespace Zentrium\Bundle\LogBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('zentrium_log.menu.log', [
            'route' => 'logs',
            'routeParameters' => ['status' => 'open'],
            'labelAttributes' => ['icon' => 'fa fa-book'],
        ])->setExtra('routes', [['pattern' => '/^log(s|_.*)$/']]);
    }
}
