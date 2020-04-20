<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class FrontConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        echo 'catch event of type '.$this->eventName.". Dispatch it to backend consumer\n";
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
}
