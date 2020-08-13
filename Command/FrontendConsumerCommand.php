<?php

namespace Wizbii\PipelineBundle\Command;

use OldSound\RabbitMqBundle\Command\MultipleConsumerCommand;
use Symfony\Component\Console\Input\InputArgument;

class FrontendConsumerCommand extends MultipleConsumerCommand
{
    protected function configure(): void
    {
        parent::configure();

        $args = array_filter($this->getDefinition()->getArguments(), function (InputArgument $arg) {
            return $arg->getName() !== 'name';
        });

        $this->getDefinition()->setArguments($args);

        $this->setDescription('Consumes all frontend queues')
            ->setName('wizbii:pipeline:consume-frontends')
            ->addArgument('name', InputArgument::OPTIONAL, '', 'all')
        ;
    }

    protected function getConsumerService(): string
    {
        return 'pipeline.consumer.front_multi';
    }
}
