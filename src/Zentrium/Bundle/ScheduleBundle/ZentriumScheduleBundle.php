<?php

namespace Zentrium\Bundle\ScheduleBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zentrium\Bundle\ScheduleBundle\DependencyInjection\Compiler\ScheduleConstraintPass;

class ZentriumScheduleBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ScheduleConstraintPass());
    }
}
