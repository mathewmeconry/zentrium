<?php

namespace Zentrium\Bundle\LogBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('zentrium_log.menu.log', [
            'route' => 'logs',
            'labelAttributes' => ['icon' => 'fa fa-book'],
        ])->setExtra('routes', [['pattern' => '/^log_.*/']]);

        $menu->addChild('zentrium_log.menu.list', ['route' => 'logs']);
        $menu->addChild('zentrium_log.menu.new', ['route' => 'log_new']);
    }
}
