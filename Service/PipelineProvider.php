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
        if ($this->pipeline === null) {
            throw new \LogicException('No current pipeline configured');
        }

        return $this->pipeline;
    }

    /**
     * @param Pipeline $pipeline
     */
    public function setPipeline($pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    /**
     * PipelineProvider constructor.
     *
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
     * @var Pipeline|null
     */
    protected $pipeline;
}
