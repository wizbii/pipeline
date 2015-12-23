<?php

namespace Wizbii\PipelineBundle\Tests\MarkdownReader;

use Wizbii\CommonBundle\Tests\BaseTestCase;
use Wizbii\PipelineBundle\Reader\MarkdownReader;

class MarkdownReaderTest extends BaseTestCase
{
    /**
     * @var MarkdownReader
     */
    protected $markdownReader;

    protected function setup()
    {
        parent::setup();
        $this->markdownReader = new MarkdownReader();
    }

    /**
     * @test
     */
    public function readSinglePipeline()
    {
        $configuration =
'# pipeline example
profile
   - full
      - proxy
      - cache
   - stats';

        $stream = $this->createStreamFromString($configuration);
        $pipelines = $this->markdownReader->read($stream);
        var_dump($pipelines);
    }

    /**
     * @param string $string
     * @return resource
     */
    protected function createStreamFromString($string)
    {
        $stream = fopen('php://memory','r+');
        fwrite($stream, $string);
        rewind($stream);
        return $stream;
    }
}
