<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Wizbii\PipelineBundle\Dispatcher\Action\ActionDispatcherInterface;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class BackConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        $content = json_decode($msg->body, true);
        $eventName = $content["event_name"];
        $eventContent = json_decode($content["original_body"], true);
        $pipeline = $this->pipelineProvider->getCurrentPipeline();
        $actionCreator = $pipeline->getActionCreatorFor($eventName);
        $action = $actionCreator->buildAction($eventName, $eventContent);
        $this->actionDispatcher->dispatch($action);
    }

    /**
     * @var PipelineProvider
     */
    public $pipelineProvider;

    /**
     * @var ActionDispatcherInterface
     */
    public $actionDispatcher;
}