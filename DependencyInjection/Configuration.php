<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 00:34
 */

namespace Vaderlab\EAV\Core\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vaderlab_eav_core');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('entity')
                    ->children()
                        ->arrayNode('value_types')->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}