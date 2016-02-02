<?php

namespace Wizbii\PipelineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Wizbii\PipelineBundle\Builder\PipelineBuilder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WizbiiPipelineExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->register("wizbii.pipeline.factory", 'Wizbii\PipelineBundle\Factory\PipelineFactory');
        $pipelineDefinition = new Definition('Wizbii\PipelineBundle\Service\Pipeline', [$config]);
        $pipelineDefinition->setFactory([
            new Reference("wizbii.pipeline.factory"),
            "getPipeline"
        ]);
        $container->setDefinition("wizbii.pipeline", $pipelineDefinition);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {

    }
}
