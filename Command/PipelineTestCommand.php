<?php

namespace Wizbii\PipelineBundle\Command;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wizbii\PipelineBundle\Service\Pipeline;

/**
 * @DI\Service
 * @DI\Tag("console.command")
 */
class PipelineTestCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo "ola\n";
        var_dump($this->pipeline);
    }

    protected function configure()
    {
        $this
            ->setName('wizbii:pipeline:test')
        ;
    }

    /**
     * @var Pipeline
     * @DI\Inject("wizbii.pipeline")
     */
    public $pipeline;
}