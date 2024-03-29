<?php

namespace Wizbii\PipelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wizbii_pipeline');

        /** @var ArrayNodeDefinition */
        $rootNode
            ->children()
                ->scalarNode('name')->end()
                ->arrayNode('consumers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('idle_timeout')
                            ->info('Exit consumers after an idle period, in seconds')
                            ->defaultValue(0)
                        ->end()
                        ->integerNode('max_execution_time')
                            ->info('Maximum execution time per consumer, in seconds')
                            ->defaultValue(0)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                        ->scalarNode('user')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('vhost')->end()
                    ->end()
                ->end()
                ->arrayNode('actions')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')->values(['direct', 'funnel'])->defaultValue('funnel')->end()
                            ->booleanNode('with_priority')->defaultValue(false)->end()
                            ->arrayNode('triggered_by_events')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stores')
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('service')->end()
                                ->arrayNode('triggered_by_actions')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('triggered_by_stores')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('triggered_event')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
