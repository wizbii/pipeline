<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

use Wizbii\PipelineBundle\Model\DataBag;

interface EventsGenerator
{
    /**
     * @return iterable<DataBag>
     */
    public function produce();
}
