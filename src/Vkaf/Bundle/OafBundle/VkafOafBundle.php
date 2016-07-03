<?php

namespace Vkaf\Bundle\OafBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vkaf\Bundle\OafBundle\DependencyInjection\Compiler\KioskSlidePass;

class VkafOafBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new KioskSlidePass());
    }
}
