<?php

namespace Wizbii\PipelineBundle\Producer;

use Psr\Log\LoggerInterface;

class Producer extends \OldSound\RabbitMqBundle\RabbitMq\Producer
{
    public function publish($msgBody, $routingKey = '', $additionalProperties = [], array $headers = null): void
    {
        try {
            parent::publish($msgBody, $routingKey, $additionalProperties, $headers);
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->error("Can't publish message. Error is ".$e->getMessage());
            }
        }
    }

    /**
     * @var LoggerInterface
     */
    public $logger;
}
