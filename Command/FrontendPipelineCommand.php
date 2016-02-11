<?php

namespace Wizbii\PipelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wizbii\PipelineBundle\Service\Pipeline;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class FrontendPipelineCommand extends ContainerAwareCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumers = $this->getConsumers();
        echo join(" ", $consumers->keys());
    }

    protected function configure()
    {
        $this
            ->setName('pipeline:frontend:list')
        ;
    }

    protected function getConsumers()
    {
        return $this->getContainer()->get("pipeline.consumers");
    }
}