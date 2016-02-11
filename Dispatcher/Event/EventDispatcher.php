<?php

namespace Wizbii\PipelineBundle\Dispatcher\Event;

use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Service\Producers;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param array $eventConfig
     * @return bool
     */
    public function dispatch($eventName, $eventConfig)
    {
        $producer = $this->producers->get($eventName);
        if (!isset($producer)) {
            $this->logger->error("Can't find producer for event '$eventName'");
            var_dump($this->producers->keys());
            return false;
        }
        echo "[EventDispatcher] Going to dispatch " . $eventName . " with content : " . json_encode($eventConfig) . "\n";
        $producer->publish(json_encode($eventConfig));

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