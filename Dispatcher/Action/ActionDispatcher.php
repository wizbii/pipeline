<?php

namespace Wizbii\PipelineBundle\Dispatcher\Action;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wizbii\PipelineBundle\Dispatcher\Event\EventDispatcherInterface;
use Wizbii\PipelineBundle\Exception\StoreNotRunnableException;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\DataBag;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Runnable\StoreInterface as RunnableStore;
use Wizbii\PipelineBundle\Service\PipelineProvider;

class ActionDispatcher implements ActionDispatcherInterface
{
    /**
     * @param Action $action
     * @return bool
     */
    public function dispatch($action)
    {
        echo "[ActionDispatcher] Going to dispatch action " . $action->getName() . " whose content is : " . json_encode($action->getProperties()) . "\n";
        $success = true;
        foreach ($this->pipelineProvider->getCurrentPipeline()->getStores() as $store) {
            if ($store->isTriggeredByAction($action)) {
                echo "  -> store " . $store->getName() . " is triggered by action " . $action->getName() . "\n";
                try {
                    $this->runStore($store, $action);
                }
                catch (StoreNotRunnableException $e) {
                    $this->logger->error("Can't dispatch action '" . $action->getName() . "'. Message was : " . $e->getMessage());
                    $success = false;
                }
            }
            else {
                //echo "  -> store " . $store->getName() . " is not triggered by action " . $action->getName() . "\n";
            }
        }

        return $success;
    }

    /**
     * @param Store $store
     * @param Action $action
     * @throws StoreNotRunnableException
     */
    protected function runStore($store, $action)
    {
        echo "    -> going to run store " . $store->getName() . "\n";
        $runner = $this->container->get($store->getService(), ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (!isset($runner)) {
            throw new StoreNotRunnableException("Store " . $store->getName() . " is not runnable : service with name '" . $store->getService() . "' does not exist'");
        }
        if (! $runner instanceof RunnableStore) {
            throw new StoreNotRunnableException("Service '" . $store->getService() . '\' does not implement \Wizbii\PipelineBundle\Runnable\Store interface');
        }

        try {
            $eventsData = $runner->run($action);
            if ($store->hasTriggeredEvent() && isset($eventsData)) {
                if ($eventsData instanceof DataBag) $eventsData = [$eventsData];
                foreach ($eventsData as $eventData) {
                    $this->eventDispatcher->dispatch($store->getTriggeredEvent()->getName(), $eventData->all());
                }
            }
        }
        catch (\Exception $e) {
            $this->logger->warning("Store " . $store->getName() . " has thrown an exception. Message was : " . $e->getMessage() . ". It occurs at " . $e->getFile() . ":" . $e->getLine());
        }

        // run next stores
        foreach ($this->pipelineProvider->getCurrentPipeline()->getStores() as $anotherStore) {
            if ($anotherStore->isTriggeredByStore($store)) {
                $this->runStore($anotherStore, $action);
            }
        }
    }

    /**
     * @var PipelineProvider
     */
    public $pipelineProvider;

    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var EventDispatcherInterface
     */
    public $eventDispatcher;
}