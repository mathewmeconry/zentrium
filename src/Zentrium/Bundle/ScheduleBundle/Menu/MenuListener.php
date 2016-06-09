<?php

namespace Zentrium\Bundle\ScheduleBundle\Menu;

use Zentrium\Bundle\CoreBundle\Menu\ConfigureMenuEvent;

class MenuListener
{
    public function onCreateMainMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu()->addChild('zentrium_schedule.menu.schedule', ['route' => 'schedules', 'labelAttributes' => ['icon' => 'fa fa-calendar']]);

        $menu->addChild('zentrium_schedule.menu.overview', ['route' => 'schedules']);
        $menu->addChild('zentrium_schedule.menu.requirements', ['route' => 'schedule_requirements']);
        $menu->addChild('zentrium_schedule.menu.tasks', ['route' => 'schedule_tasks']);
        $menu->addChild('zentrium_schedule.menu.users', ['route' => 'schedule_users']);
        $menu->addChild('zentrium_schedule.menu.skills', ['route' => 'schedule_skills']);
    }
}
