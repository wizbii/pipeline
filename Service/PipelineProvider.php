<?php

namespace Wizbii\PipelineBundle\Service;

use Wizbii\PipelineBundle\Factory\PipelineFactory;
use Wizbii\PipelineBundle\Model\Pipeline;
class PipelineProvider
{
    /**
     * @return Pipeline
     */
    public function getCurrentPipeline()
    {
        return $this->pipeline;
    }

    /**
     * PipelineProvider constructor.
     * @param array $pipelineConfiguration
     */
    public function __construct($pipelineConfiguration)
    {
        $pipelineFactory = new PipelineFactory();
        $this->pipeline = $pipelineFactory->buildPipeline($pipelineConfiguration);
    }

    /**
     * @var Pipeline
     */
    protected $pipeline;
}