<?php

namespace Zentrium\Bundle\MapBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;
use Zentrium\Bundle\MapBundle\Entity\MapManager;

class MenuListener
{
    private $manager;

    public function __construct(MapManager $manager)
    {
        $this->manager = $manager;
    }

    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('zentrium_map.menu.map', [
            'route' => 'maps',
            'labelAttributes' => ['icon' => 'fa fa-map-marker'],
        ]);

        foreach ($this->manager->findAllWithNames() as $map) {
            $menu->addChild($map->getId(), [
                'route' => 'map_view',
                'routeParameters' => ['map' => $map->getId()],
                'label' => $map->getName(),
                'labelAttributes' => ['translate' => false],
            ]);
        }

        $menu->addChild('zentrium_map.menu.new', [
            'route' => 'map_new',
            'labelAttributes' => ['icon' => 'fa fa-plus'],
        ]);
    }
}
