<?php

namespace Wizbii\PipelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $consumers = $this->getConsumers($input->getOption('direct'));

        echo join(" ", $consumers->getArrayCopy());
    }

    protected function configure()
    {
        $this
            ->setName('pipeline:frontend:list')
            ->addOption('direct', null, InputOption::VALUE_NONE)
        ;
    }

    protected function getConsumers(bool $directConsumers)
    {
        $serviceId = $directConsumers ? "pipeline.consumers.direct" : "pipeline.consumers";

        return $this->getContainer()->get($serviceId);
    }
}