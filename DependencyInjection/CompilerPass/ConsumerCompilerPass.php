<?php

namespace Wizbii\PipelineBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsumerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('pipeline.consumers')) {
            return;
        }

        $definition = $container->getDefinition('pipeline.consumers');

        foreach ($container->findTaggedServiceIds('pipeline.front.consumer') as $id => $attributes) {
            list(, $name) = explode(".", $id);
            $name = str_replace("_consumer", "", $name);
            $definition->addMethodCall('set', [$name, new Reference($id)]);
        }
    }
}