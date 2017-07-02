<?php

namespace Vkaf\Bundle\OafBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vkaf\Bundle\OafBundle\DependencyInjection\Compiler\KioskSlidePass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\RoleRegistrationPass;

class VkafOafBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new KioskSlidePass());

        $container->addCompilerPass(new RoleRegistrationPass([
            'ROLE_OAF_RESOURCE_MANAGE' => ['vkaf_oaf.role.resource_manage', ['ROLE_MANAGER']],
        ]));
    }
}
