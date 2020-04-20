<?php

namespace Wizbii\PipelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FrontendPipelineCommand extends ContainerAwareCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumers = $this->getConsumers($input->getOption('direct'));
        $asJson = $input->getOption('json');

        $output->write($asJson ? json_encode($consumers) : join(' ', $consumers));

        return 0;
    }

    protected function configure(): void
    {
        $this
            ->setName('pipeline:frontend:list')
            ->addOption('direct', null, InputOption::VALUE_NONE)
            ->addOption('json', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * @return string[]
     */
    protected function getConsumers(bool $directConsumers): array
    {
        $serviceId = $directConsumers ? 'pipeline.consumers.direct' : 'pipeline.consumers';

        return $this->getContainer()->get($serviceId)->getArrayCopy();
    }
}
