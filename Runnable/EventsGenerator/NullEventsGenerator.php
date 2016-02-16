<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

class NullEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        // nothing to do
    }
}