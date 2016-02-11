<?php

namespace Wizbii\PipelineBundle\Messaging;

interface MessagingInterface
{
    /**
     * @param string $eventName
     * @param array $eventData
     * @param array $options
     * @return bool
     */
    public function publish($eventName, $eventData, $options = []);
}