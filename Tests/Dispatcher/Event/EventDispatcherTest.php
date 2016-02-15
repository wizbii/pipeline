<?php

namespace Wizbii\PipelineBundle\Tests\Dispatcher\Event;

use Psr\Log\NullLogger;
use Wizbii\PipelineBundle\Dispatcher\Event\EventDispatcher;
use Wizbii\PipelineBundle\Service\Producers;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class EventDispatcherTest extends BaseTestCase
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->logger = new NullLogger();
        $this->eventDispatcher->producers = New Producers();
    }

    /**
     * @test
     */
    public function dispatch()
    {
        $producer = $this->getMock('OldSound\RabbitMqBundle\RabbitMq\ProducerInterface');
        $producer->expects($this->once())->method("publish");
        $this->eventDispatcher->producers->set("profile_created", $producer);

        $returnedValue = $this->eventDispatcher->dispatch("profile_created", ["profile_id" => "john"]);
        $this->assertThat($returnedValue, $this->isTrue());
    }

    /**
     * @test
     */
    public function unsupportedEvent()
    {
        $producer = $this->getMock('OldSound\RabbitMqBundle\RabbitMq\ProducerInterface');
        $producer->expects($this->never())->method("publish");
        $this->eventDispatcher->producers->set("profile_created", $producer);

        $returnedValue = $this->eventDispatcher->dispatch("company_created", ["profile_id" => "wizbii"]);
        $this->assertThat($returnedValue, $this->isFalse());
    }
}