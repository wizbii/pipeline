<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

use Wizbii\PipelineBundle\Model\DataBag;

interface EventsGenerator
{
    /**
     * @return DataBag
     */
    public function produce();
}