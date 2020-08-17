<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Dispatcher\Action\ActionDispatcherInterface;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class BackConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        $content = json_decode($msg->body, true, 512, \JSON_THROW_ON_ERROR);
        $eventName = $content['event_name'];
        $eventContent = json_decode($content['original_body'], true, 512, \JSON_THROW_ON_ERROR);
        $pipeline = $this->pipelineProvider->getCurrentPipeline();
        $actionCreator = $pipeline->getActionCreatorFor($eventName);

        if (isset($actionCreator)) {
            $action = $actionCreator->buildAction($eventName, $eventContent);
            $this->actionDispatcher->dispatch($action);
        } else {
            $this->logger->error("Can't find any valid action creator for event '$eventName");
        }
    }

    /**
     * @var PipelineProvider
     */
    public $pipelineProvider;

    /**
     * @var ActionDispatcherInterface
     */
    public $actionDispatcher;

    /**
     * @var LoggerInterface
     */
    public $logger;
}
