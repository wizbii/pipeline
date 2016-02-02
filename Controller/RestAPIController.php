<?php

namespace Wizbii\PipelineBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Wizbii\PipelineBundle\Service\Pipeline;

/**
 * @DI\Service("wizbii.pipeline.rest.api.controller")
 */
class RestAPIController
{
    public function getPipeline()
    {
        return new Response($this->serializer->serialize($this->pipeline, "json"));
    }

    /**
     * @var Serializer
     * @DI\Inject("jms_serializer")
     */
    public $serializer;

    /**
     * @var Pipeline
     * @DI\Inject("wizbii.pipeline")
     */
    public $pipeline;
} 