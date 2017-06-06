<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zentrium\Bundle\CoreBundle\Request\RequestBodyParamConverter;

/**
 * Replaces the RequestBodyParamConverter in FOSRestBundle.
 */
class RequestBodyParamConverterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_rest.converter.request_body');
        $definition->setClass(RequestBodyParamConverter::class);
    }
}
