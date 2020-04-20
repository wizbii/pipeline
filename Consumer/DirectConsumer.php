<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Dispatcher\Action\ActionDispatcherInterface;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class DirectConsumer implements ConsumerInterface
{
    /**
     * @var PipelineProvider
     */
    private $pipelineProvider;

    /**
     * @var ActionDispatcherInterface
     */
    private $actionDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $eventName;

    public function __construct(
        string $eventName,
        PipelineProvider $pipelineProvider,
        ActionDispatcherInterface $actionDispatcher,
        LoggerInterface $logger
    ) {
        $this->eventName = $eventName;
        $this->pipelineProvider = $pipelineProvider;
        $this->actionDispatcher = $actionDispatcher;
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $msg)
    {
        $eventContent = json_decode($msg->body, true);

        if (json_last_error()) {
            $this->logger->error(sprintf("Invalid json: %s, body: %s", json_last_error_msg(), $msg->body));
            return;
        }

        $pipeline = $this->pipelineProvider->getCurrentPipeline();
        $actionCreator = $pipeline->getActionCreatorFor($this->eventName);

        if (!$actionCreator) {
            $this->logger->error("Can't find any valid action creator for event '$this->eventName");
            return;
        }

        $action = $actionCreator->buildAction($this->eventName, $eventContent);

        $this->actionDispatcher->dispatch($action);
    }
}
