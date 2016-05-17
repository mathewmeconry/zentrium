<?php

namespace Zentrium\Bundle\CoreBundle\Dashboard;

use Symfony\Component\EventDispatcher\Event;

class BuildDashboardEvent extends Event
{
    const POSITION_TOP = 'top';
    const POSITION_CENTER = 'center';
    const POSITION_SIDEBAR = 'sidebar';

    private $widgets;

    public function __construct()
    {
        $this->widgets = [];
    }

    public function addWidget($position, $content, $priority = 0)
    {
        $this->widgets[] = [
            'position' => $position,
            'content' => $content,
            'priority' => $priority,
        ];

        return $this;
    }

    public function getWidgets($position = null)
    {
        if ($position !== null) {
            $widgets = array_filter($this->widgets, function ($widget) use ($position) {
                return ($widget['position'] === $position);
            });
        } else {
            $widget = $this->widgets;
        }

        usort($widgets, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return $widgets;
    }
}
