<?php

namespace Zentrium\Bundle\ScheduleBundle\Tests;

use Zentrium\Bundle\CoreBundle\Tests\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected function getAllFixtureClasses()
    {
        $classes = parent::getAllFixtureClasses();
        $classes[] = 'Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM\LoadRequirementSetData';
        $classes[] = 'Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM\LoadScheduleData';
        $classes[] = 'Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM\LoadSkillData';
        $classes[] = 'Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM\LoadTaskData';
        $classes[] = 'Zentrium\Bundle\ScheduleBundle\Tests\DataFixtures\ORM\LoadUserData';

        return $classes;
    }
}
