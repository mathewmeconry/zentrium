<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces the RouteMatcher in Dunglas' CSRF bundle.
 */
class CsrfRouteMatcherPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $listenerDefinition = $container->getDefinition('dunglas_angular_csrf.validation_listener');
        $listenerDefinition->replaceArgument(1, new Reference('zentrium.csrf.route_matcher'));

        $formExtensionDefinition = $container->getDefinition('dunglas_angular_csrf.form.extension.disable_csrf');
        $formExtensionDefinition->replaceArgument(1, new Reference('zentrium.csrf.route_matcher'));
    }
}
