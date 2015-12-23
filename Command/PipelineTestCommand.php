<?php

namespace Wizbii\PipelineBundle\Command;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wizbii\PipelineBundle\Reader\MarkdownReader;

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
        $markdownReader = new MarkdownReader();
        $configuration =
            '# pipeline example
profile
   - full
      - proxy
      - cache
   - stats';
        $stream = fopen('php://memory','r+');
        fwrite($stream, $configuration);
        rewind($stream);
        $pipelines = $markdownReader->read($stream);
        var_dump($pipelines);
    }

    protected function configure()
    {
        $this
            ->setName('wizbii:pipeline:test')
        ;
    }
}