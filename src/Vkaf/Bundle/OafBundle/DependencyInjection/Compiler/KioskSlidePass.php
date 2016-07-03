<?php

namespace Vkaf\Bundle\OafBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds services tagged with vkaf_oaf.kiosk.slide
 * to the slide manager.
 */
class KioskSlidePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('vkaf_oaf.kiosk.slide_manager')) {
            return;
        }

        $definition = $container->getDefinition('vkaf_oaf.kiosk.slide_manager');

        foreach ($container->findTaggedServiceIds('vkaf_oaf.kiosk.slide') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \RuntimeException(sprintf('The type for the tag "vkaf_oaf.kiosk.slide" of service "%s" must be set.', $id));
            }

            $definition->addMethodCall('registerType', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
