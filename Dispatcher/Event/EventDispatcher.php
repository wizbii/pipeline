<?php

namespace Wizbii\PipelineBundle\Dispatcher\Event;

use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Model\DataBag;
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

        $additionalProperties = [];

        if (isset($eventConfig[DataBag::OPTION_PRIORITY])) {
            $priority = $eventConfig[DataBag::OPTION_PRIORITY];

            if (is_int($priority) && $priority >= 0 && $priority <= 10) {
                $additionalProperties['priority'] = $priority;
            } else {
                $this->logger->error('Priority must be an integer between 0-10', [
                    'event_name' => $eventName,
                    'event_properties' => $eventConfig,
                    'priority' => $priority,
                ]);
            }

            unset($eventConfig[DataBag::OPTION_PRIORITY]);
        }

        $producer->publish(json_encode($eventConfig, JSON_THROW_ON_ERROR), '', $additionalProperties);

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
