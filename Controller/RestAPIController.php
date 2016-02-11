<?php

namespace Wizbii\PipelineBundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class RestAPIController
{
    public function getPipeline()
    {
        //var_dump($this->pipelineProvider->getCurrentPipeline()); exit;
        return new Response($this->serializer->serialize($this->pipelineProvider->getCurrentPipeline(), "json"));
    }

    /**
     * @var Serializer
     */
    public $serializer;

    /**
     * @var PipelineProvider
     */
    public $pipelineProvider;
} 