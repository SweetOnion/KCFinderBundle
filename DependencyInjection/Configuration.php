<?php

namespace Ikadoc\KCFinderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): NodeParentInterface
    {
        $treeBuilder = new TreeBuilder('ikadoc_kc_finder');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('base_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->append($this->createConfigsNode())
            ->end()
        ;

        return $treeBuilder;
    }

    protected function createConfigsNode(): NodeDefinition
    {
        return (new TreeBuilder('config'))->getRootNode()
            ->useAttributeAsKey('name')
            ->prototype('variable')
        ->end();
    }
}
