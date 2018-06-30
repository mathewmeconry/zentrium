<?php

namespace Vkaf\Bundle\OafBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vkaf_oaf');

        $rootNode
            ->children()
                ->arrayNode('lineup')
                    ->isRequired()
                    ->children()
                        ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('pushpin')
                    ->isRequired()
                    ->children()
                        ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('api')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('base_url')->end()
                    ->end()
                ->end()
                ->arrayNode('sms')
                    ->isRequired()
                    ->children()
                        ->scalarNode('sender_id')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('send_topic')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('status_topic')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
