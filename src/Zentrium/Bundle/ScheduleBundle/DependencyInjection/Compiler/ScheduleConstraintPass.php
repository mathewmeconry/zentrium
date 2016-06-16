<?php

namespace Zentrium\Bundle\ScheduleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged zentrium.schedule.constraint_builder and
 * zentrium.schedule.constraint_validator services to the registry.
 */
class ScheduleConstraintPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('zentrium_schedule.schedule.constraint_registry')) {
            return;
        }

        $definition = $container->getDefinition('zentrium_schedule.schedule.constraint_registry');

        foreach ($container->findTaggedServiceIds('zentrium_schedule.schedule.constraint_builder') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \RuntimeException(sprintf('The type for the tag "zentrium_schedule.schedule.constraint_builder" of service "%s" must be set.', $id));
            }

            $definition->addMethodCall('addBuilder', [$attributes[0]['type'], new Reference($id)]);
        }

        foreach ($container->findTaggedServiceIds('zentrium_schedule.schedule.constraint_validator') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \RuntimeException(sprintf('The type for the tag "zentrium_schedule.schedule.constraint_validator" of service "%s" must be set.', $id));
            }

            $definition->addMethodCall('addValidator', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
