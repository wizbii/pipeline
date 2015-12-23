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
        $this->assertEquals(1, count($pipelines));
        $pipeline = $pipelines[0];
        $this->assertEquals("profile", $pipeline->getName());
        $this->assertEquals(2, count($pipeline->getFollowingTasks()));
        $fullTask = $pipeline->getFollowingTasks()[0];
        $this->assertEquals("full", $fullTask->getName());
        $this->assertEquals(2, count($fullTask->getFollowingTasks()));
        $proxyTask = $fullTask->getFollowingTasks()[0];
        $this->assertEquals("proxy", $proxyTask->getName());
        $this->assertEquals(0, count($proxyTask->getFollowingTasks()));
    }

    /**
     * @test
     */
    public function readMultiplePipelines()
    {
        $configuration =
            '// first pipeline with 3 spaces as indentation
profile
   - full
      - proxy
      - cache
   - stats

# You can insert comments in
   // the middle
 # of your text

// second pipeline with 2 spaces as indentation. It\'s as you want :)
job
  - step with space-and_other.special!characters

';

        $stream = $this->createStreamFromString($configuration);
        $pipelines = $this->markdownReader->read($stream);
        $this->assertEquals(2, count($pipelines));
        $pipeline = $pipelines[1];
        $this->assertEquals("job", $pipeline->getName());
        $this->assertEquals(1, count($pipeline->getFollowingTasks()));
        $task = $pipeline->getFollowingTasks()[0];
        $this->assertEquals("step with space-and_other.special!characters", $task->getName());
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
