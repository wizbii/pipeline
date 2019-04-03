<?php

namespace Tests\Consumer;

use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Wizbii\PipelineBundle\Consumer\DirectConsumer;
use Wizbii\PipelineBundle\Dispatcher\Action\ActionDispatcherInterface;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Pipeline;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class DirectConsumerTest extends TestCase
{
    const EVENT_NAME = "event_name";

    private $consumer;

    public function setup()
    {
        $this->pipelineProvider = $this->prophesize(PipelineProvider::class);
        $this->actionDispatcher = $this->prophesize(ActionDispatcherInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->pipeline = $this->prophesize(Pipeline::class);

        $this->pipelineProvider->getCurrentPipeline()->willReturn($this->pipeline->reveal());

        $this->consumer = new DirectConsumer(
            self::EVENT_NAME,
            $this->pipelineProvider->reveal(),
            $this->actionDispatcher->reveal(),
            $this->logger->reveal()
        );
    }

    public function testExecuteDispatchAnAction()
    {
        $msg = new AMQPMessage('{"foo": "bar"}');

        $actionCreator = $this->prophesize(ActionCreator::class);
        $action = $this->prophesize(Action::class);

        $this->pipeline->getActionCreatorFor(self::EVENT_NAME)->willReturn($actionCreator);

        $actionCreator->buildAction(self::EVENT_NAME, ['foo' => 'bar'])
            ->shouldBeCalled()
            ->willReturn($action->reveal());

        $this->actionDispatcher->dispatch($action->reveal())->shouldBeCalled();

        $this->consumer->execute($msg);
    }

    public function testExecuteWithInvalidJson()
    {
        $msg = new AMQPMessage('{foo: bar}');

        $this->logger->error(Argument::any())->shouldBeCalled();

        $this->actionDispatcher->dispatch(Argument::any())->shouldNotBeCalled();

        $this->consumer->execute($msg);
    }

    public function testExecuteDispatchNothingOnNotFoundAction()
    {
        $msg = new AMQPMessage('{"foo": "bar"}');

        $this->pipeline->getActionCreatorFor(self::EVENT_NAME)->willReturn(null);

        $this->actionDispatcher->dispatch(Argument::any())->shouldNotBeCalled();

        $this->consumer->execute($msg);
    }
}
