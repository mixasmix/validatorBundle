<?php

namespace mixasmix\ValidationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('fingineers_validation');

        $rootNode = $builder->getRootNode();
        $rootNode->children()
            ->scalarNode('url')
            ->isRequired()
            ->defaultValue('')
            ->end()
            ->end();

        return $builder;
    }
}
