<?php

namespace Vkaf\Bundle\OafBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('vkaf_oaf.menu.resource', [
            'route' => 'oaf_resource_assign',
            'labelAttributes' => ['icon' => 'fa fa-square'],
        ]);

        $menu->addChild('vkaf_oaf.menu.resource_assign', [
            'route' => 'oaf_resource_assign',
        ]);

        $menu->addChild('vkaf_oaf.menu.resource', [
            'route' => 'oaf_resources',
        ]);
    }
}
