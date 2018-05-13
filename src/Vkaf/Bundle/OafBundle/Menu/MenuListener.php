<?php

namespace Vkaf\Bundle\OafBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $resourceMenu = $event->getMenu()->addChild('vkaf_oaf.menu.resource', [
            'route' => 'oaf_resource_assign',
            'labelAttributes' => ['icon' => 'fa fa-square'],
        ]);

        $resourceMenu->addChild('vkaf_oaf.menu.resource_assign', [
            'route' => 'oaf_resource_assign',
        ]);

        $resourceMenu->addChild('vkaf_oaf.menu.resource', [
            'route' => 'oaf_resources',
        ]);

        $announcementMenu = $event->getMenu()->addChild('vkaf_oaf.menu.notifications', [
            'route' => 'oaf_announcements',
            'labelAttributes' => ['icon' => 'fa fa-bullhorn'],
        ]);

        $announcementMenu->addChild('vkaf_oaf.menu.messages', [
            'route' => 'oaf_messages',
        ]);
    }
}
