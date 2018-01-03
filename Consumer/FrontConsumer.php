<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class FrontConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        if ($this->logger) {
            $this->logger->debug("Catched event. Dispatch it to backend consumer", [
                'event_name' => $this->eventName
            ]);
        }

        $message = [
            "original_body" => $msg->body,
            "event_name" => $this->eventName
        ];

        try {
            $this->producer->publish(json_encode($message));
        }
        catch (\Exception $e) {}
    }

    /**
     * @var string
     */
    public $eventName;

    /**
     * @var ProducerInterface
     */
    public $producer;

    /**
     * @var LoggerInterface
     */
    public $logger;
}
