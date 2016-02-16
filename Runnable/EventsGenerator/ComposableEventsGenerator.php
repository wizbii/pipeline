<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

class ComposableEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        foreach ($this->eventsGenerators as $eventsGenerator) {
            $eventsGenerator->produce();
        }
    }

    /**
     * @param EventsGenerator[] $eventsGenerators
     */
    public function setDataBags($eventsGenerators)
    {
        $this->eventsGenerators = $eventsGenerators;
    }

    /**
     * @param EventsGenerator $eventsGenerator
     */
    public function addEventsGenerator($eventsGenerator)
    {
        $this->eventsGenerators[] = $eventsGenerator;
    }

    /**
     * @var EventsGenerator[]
     */
    protected $eventsGenerators = [];
}