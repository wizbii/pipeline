<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

class NullEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        return []; // nothing to do
    }
}
