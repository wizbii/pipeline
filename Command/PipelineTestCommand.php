<?php

namespace Wizbii\PipelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wizbii\PipelineBundle\Service\Pipeline;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class PipelineTestCommand extends ContainerAwareCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$consumers = $this->getConsumers();
        var_dump($consumers->keys());*/
        var_dump($this->getPipeline());
    }

    protected function configure()
    {
        $this
            ->setName('wizbii:pipeline:test')
        ;
    }

    protected function getConsumers()
    {
        return $this->getContainer()->get("pipeline.consumers");
    }

    protected function getPipeline()
    {
        return $this->getContainer()->get("pipeline.provider")->getCurrentPipeline();
    }
}