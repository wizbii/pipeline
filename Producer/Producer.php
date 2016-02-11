<?php

namespace Wizbii\PipelineBundle\Producer;

use Psr\Log\LoggerInterface;

class Producer extends \OldSound\RabbitMqBundle\RabbitMq\Producer
{
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        try {
            parent::publish($msgBody, $routingKey, $additionalProperties);
        }
        catch (\Exception $e) {
            if (isset($this->logger)) {
                $this->logger->error("Can't publish message. Error is " . $e->getMessage());
            }
        }
    }

    /**
     * @var LoggerInterface
     */
    public $logger;

}