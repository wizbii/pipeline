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
     * @param Pipeline $pipeline
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * PipelineProvider constructor.
     * @param array $pipelineConfiguration
     */
    public function __construct($pipelineConfiguration = null)
    {
        $pipelineFactory = new PipelineFactory();
        if (isset($pipelineConfiguration)) {
            $this->pipeline = $pipelineFactory->buildPipeline($pipelineConfiguration);
        }
    }

    /**
     * @var Pipeline
     */
    protected $pipeline;
}