<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers managed roles which are assignable from the user interface.
 */
class RoleRegistrationPass implements CompilerPassInterface
{
    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Returns a map of managed roles.
     *
     * @return array
     */
    protected function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('zentrium.roles');
        foreach ($this->getRoles() as $role => $data) {
            $definition->addMethodCall('register', array_merge([$role], $data));
        }
    }
}
