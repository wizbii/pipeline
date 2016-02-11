<?php

namespace Wizbii\PipelineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Wizbii\PipelineBundle\DependencyInjection\CompilerPass\ConsumerCompilerPass;
use Wizbii\PipelineBundle\DependencyInjection\CompilerPass\ProducerCompilerPass;

class WizbiiPipelineBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConsumerCompilerPass());
        $container->addCompilerPass(new ProducerCompilerPass());
    }
}
