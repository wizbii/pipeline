<?php

namespace Wizbii\PipelineBundle\Dispatcher\Event;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param array  $eventConfig
     *
     * @return bool
     */
    public function dispatch($eventName, $eventConfig);
}
