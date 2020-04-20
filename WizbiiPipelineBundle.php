<?php

namespace Wizbii\PipelineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Wizbii\PipelineBundle\DependencyInjection\CompilerPass\ConsumerCompilerPass;
use Wizbii\PipelineBundle\DependencyInjection\CompilerPass\ProducerCompilerPass;

class WizbiiPipelineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConsumerCompilerPass());
        $container->addCompilerPass(new ProducerCompilerPass());
    }
}
