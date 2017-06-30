<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Replaces the built-in role hierarchy.
 */
class RoleHierarchyPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('security.role_hierarchy', 'zentrium.roles');
    }
}
