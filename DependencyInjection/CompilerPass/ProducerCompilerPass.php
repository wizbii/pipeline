<?php

namespace Wizbii\PipelineBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProducerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('pipeline.producers')) {
            return;
        }

        $definition = $container->getDefinition('pipeline.producers');

        foreach ($container->findTaggedServiceIds('pipeline.back.producer') as $id => $attributes) {
            $name = str_replace("pipeline.producer.", "", $id);
            $definition->addMethodCall('set', [$name, new Reference($id)]);
        }
    }
}