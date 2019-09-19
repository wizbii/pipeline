<?php

namespace Wizbii\PipelineBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsumerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->processConsumers($container, 'pipeline.consumers', 'pipeline.front.consumer');
        $this->processConsumers($container, 'pipeline.consumers.direct', 'pipeline.front.consumer.direct');
    }

    private function processConsumers(ContainerBuilder $container, string $serviceId, string $tag): void
    {
        if (false === $container->hasDefinition($serviceId)) {
            return;
        }

        $definition = $container->getDefinition($serviceId);

        foreach ($container->findTaggedServiceIds($tag) as $id => $attributes) {
            list(, $name) = explode(".", $id);
            $name = str_replace("_consumer", "", $name);
            $definition->addMethodCall('append', [$name]);
        }
    }
}
