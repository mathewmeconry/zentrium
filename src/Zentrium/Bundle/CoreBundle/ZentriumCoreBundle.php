<?php

namespace Zentrium\Bundle\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\CsrfRouteMatcherPass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\PasswordResetListenerPass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\RequestBodyParamConverterPass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\RoleHierarchyPass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\RoleRegistrationPass;
use Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler\TemplateGuesserPass;

class ZentriumCoreBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CsrfRouteMatcherPass());
        $container->addCompilerPass(new PasswordResetListenerPass());
        $container->addCompilerPass(new RequestBodyParamConverterPass());
        $container->addCompilerPass(new RoleHierarchyPass());
        $container->addCompilerPass(new RoleRegistrationPass([
            'ROLE_MANAGER' => ['zentrium.role.manager', []],
            'ROLE_ADMINISTRATOR' => ['zentrium.role.administrator', ['ROLE_MANAGER']],
        ]));
        $container->addCompilerPass(new TemplateGuesserPass());
    }
}
