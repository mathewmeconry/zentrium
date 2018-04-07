<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zentrium\Bundle\CoreBundle\Templating\TemplateGuesser;

/**
 * Replaces the template naming strategy in SensioFrameworkExtraBundle.
 */
class TemplateGuesserPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sensio_framework_extra.view.guesser');
        $definition->setClass(TemplateGuesser::class);
    }
}
