<?php

namespace Zentrium\Bundle\LogBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\RoleRegistrationPass;

class ZentriumLogBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoleRegistrationPass([
            'ROLE_LOG_READ' => ['zentrium_log.role.read', ['ROLE_MANAGER']],
            'ROLE_LOG_WRITE' => ['zentrium_log.role.write', ['ROLE_LOG_READ']],
        ]));
    }
}
