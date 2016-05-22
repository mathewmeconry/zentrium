<?php

namespace Zentrium\Bundle\MapBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $event->getMenu()->addChild('zentrium_map.menu.map', ['route' => 'maps', 'labelAttributes' => ['icon' => 'fa fa-map-marker']]);
    }
}
