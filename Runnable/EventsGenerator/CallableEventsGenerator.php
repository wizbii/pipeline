<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

class CallableEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        return call_user_func_array($this->callable, []);
    }

    /**
     * CallableEventsGenerator constructor.
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @var callable
     */
    protected $callable;
}
