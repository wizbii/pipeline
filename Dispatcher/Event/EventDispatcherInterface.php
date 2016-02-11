<?php

namespace Wizbii\PipelineBundle\Dispatcher\Event;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param array $eventConfig
     * @return boolean
     */
    public function dispatch($eventName, $eventConfig);
}