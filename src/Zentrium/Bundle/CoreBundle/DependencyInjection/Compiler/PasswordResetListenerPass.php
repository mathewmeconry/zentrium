<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zentrium\Bundle\CoreBundle\Security\PasswordResetListener;

/**
 * Replaces the RessetingListener in FOSUserBundle.
 */
class PasswordResetListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_user.listener.resetting');
        $definition->setClass(PasswordResetListener::class);
    }
}
