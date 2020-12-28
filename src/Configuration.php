<?php

namespace TKusy\JSchema;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('a2jschema');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('rootNamespace')->end()
                ->scalarNode('idPrefix')->defaultValue('http://json-schema.org/schema/')->end()
                ->arrayNode('destination')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->defaultNull()->end()
                        ->scalarNode('pathTemplate')->defaultValue('%s.schema.json')->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
