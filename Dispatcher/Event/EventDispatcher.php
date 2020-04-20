<?php

namespace Wizbii\PipelineBundle\Dispatcher\Event;

use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Service\Producers;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param array  $eventConfig
     *
     * @return bool
     */
    public function dispatch($eventName, $eventConfig)
    {
        $producer = $this->producers->get($eventName);

        if ($producer === null) {
            $this->logger->error("Can't find producer to dispatch event.", [
                'event_name' => $eventName,
                'available_producers' => $this->producers->keys(),
            ]);

            return false;
        }

        $this->logger->info('Dispatching event.', [
            'event_name' => $eventName,
            'event_properties' => $eventConfig,
        ]);

        $producer->publish(json_encode($eventConfig, JSON_THROW_ON_ERROR));

        return true;
    }

    /**
     * @var Producers
     */
    public $producers;

    /**
     * @var LoggerInterface
     */
    public $logger;
}
