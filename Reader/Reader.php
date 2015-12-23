<?php

namespace Wizbii\PipelineBundle\Reader;

use Wizbii\PipelineBundle\Model\Pipeline;

interface Reader
{
    /**
     * @param resource $stream
     * @param array $options
     * @return Pipeline[]
     */
    public function read($stream, $options = []);
}