<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

class ComposableEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        foreach ($this->eventsGenerators as $eventsGenerator) {
            if ($eventsGenerator instanceof NullEventsGenerator) {
                continue;
            }

            foreach ($eventsGenerator->produce() as $product) {
                yield $product;
            }
        }
    }

    /**
     * @param EventsGenerator[] $eventsGenerators
     */
    public function setDataBags($eventsGenerators): void
    {
        $this->eventsGenerators = $eventsGenerators;
    }

    /**
     * @param EventsGenerator $eventsGenerator
     */
    public function addEventsGenerator($eventsGenerator): void
    {
        $this->eventsGenerators[] = $eventsGenerator;
    }

    /**
     * @var EventsGenerator[]
     */
    protected $eventsGenerators = [];
}
