<?php

namespace Zentrium\Bundle\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\CsrfRouteMatcherPass;

class ZentriumCoreBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CsrfRouteMatcherPass());
    }
}
