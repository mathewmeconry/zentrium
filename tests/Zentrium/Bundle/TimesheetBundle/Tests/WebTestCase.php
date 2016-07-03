<?php

namespace Zentrium\Bundle\TimesheetBundle\Tests;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected function getAllFixtureClasses()
    {
        $classes = parent::getAllFixtureClasses();
        $classes[] = 'Zentrium\Bundle\TimesheetBundle\Tests\DataFixtures\ORM\LoadActivityData';
        $classes[] = 'Zentrium\Bundle\TimesheetBundle\Tests\DataFixtures\ORM\LoadEntryData';

        return $classes;
    }
}
