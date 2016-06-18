<?php

namespace Zentrium\Bundle\CoreBundle\Dashboard;

class Position
{
    const TOP = 'top';
    const CENTER = 'center';
    const SIDEBAR = 'sidebar';

    public static function all()
    {
        return [
            self::TOP,
            self::SIDEBAR,
            self::CENTER,
        ];
    }
}
