<?php

namespace Wizbii\PipelineBundle\Tests\Dispatcher\Action;

use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Container;
use Wizbii\PipelineBundle\Dispatcher\Action\ActionDispatcher;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\DataBag;
use Wizbii\PipelineBundle\Model\Pipeline;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Runnable\BaseStore;
use Wizbii\PipelineBundle\Service\PipelineProvider;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class ActionDispatcherTest extends BaseTestCase
{
    /**
     * @var ActionDispatcher
     */
    protected $actionDispatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->actionDispatcher = new ActionDispatcher();
        $this->actionDispatcher->logger = new NullLogger();
        $this->actionDispatcher->pipelineProvider = new PipelineProvider();
    }

    /**
     * @test
     */
    public function dispatch()
    {
        // init container
        $runnableStore = $this->getMock('Wizbii\PipelineBundle\Runnable\StoreInterface');
        $runnableStore->expects($this->once())->method("run");
        $container = new Container();
        $container->set("pipeline.store.profile.dashboard", $runnableStore);
        $this->actionDispatcher->container = $container;

        // init pipeline
        $action = new Action("profile_created");
        $store = new Store("profile_network");
        $store->setService("pipeline.store.profile.dashboard");
        $store->addTriggeredByAction($action);
        $pipeline = new Pipeline();
        $pipeline->addStore($store);
        $this->actionDispatcher->pipelineProvider->setPipeline($pipeline);

        // dispatch action
        $returnedValue = $this->actionDispatcher->dispatch($action);
        $this->assertThat($returnedValue, $this->isTrue());
    }

    /**
     * @test
     */
    public function storeIsNotRunnable()
    {
        // init container
        $container = new Container();
        $this->actionDispatcher->container = $container;

        // init pipeline
        $action = new Action("profile_created");
        $store = new Store("profile_network");
        $store->setService("pipeline.store.profile.dashboard");
        $store->addTriggeredByAction($action);
        $pipeline = new Pipeline();
        $pipeline->addStore($store);
        $this->actionDispatcher->pipelineProvider->setPipeline($pipeline);

        // dispatch action
        $returnedValue = $this->actionDispatcher->dispatch($action);
        $this->assertThat($returnedValue, $this->isFalse());
    }
}