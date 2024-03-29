<?php

namespace Zentrium\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zentrium_core');

        $rootNode
            ->children()
                ->scalarNode('default_country')
                    ->cannotBeEmpty()
                    ->defaultValue('US')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
