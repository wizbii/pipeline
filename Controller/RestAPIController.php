<?php

namespace Wizbii\PipelineBundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Wizbii\PipelineBundle\Service\Pipeline;

class RestAPIController
{
    public function getPipeline()
    {
        return new Response($this->serializer->serialize($this->pipeline, "json"));
    }

    /**
     * @var Serializer
     */
    public $serializer;

    /**
     * @var Pipeline
     */
    public $pipeline;
} 