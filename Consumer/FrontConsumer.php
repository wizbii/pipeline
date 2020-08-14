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
        $this->logger->info('Catch event on frontend queue, dispatch it to backend consumer', ['event_name' => $this->eventName, 'body' => $msg->body]);

        $message = [
            'original_body' => $msg->body,
            'event_name' => $this->eventName,
        ];

        $this->producer->publish(json_encode($message, JSON_THROW_ON_ERROR));
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
