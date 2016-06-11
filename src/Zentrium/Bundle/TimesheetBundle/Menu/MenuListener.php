<?php

namespace Zentrium\Bundle\TimesheetBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('zentrium_timesheet.menu.timesheet', ['route' => 'timesheet_entries', 'labelAttributes' => ['icon' => 'fa fa-clock-o']]);
        $menu->addChild('zentrium_timesheet.menu.entries', ['route' => 'timesheet_entries']);
        $menu->addChild('zentrium_timesheet.menu.activity', ['route' => 'timesheet_activities']);
    }
}
