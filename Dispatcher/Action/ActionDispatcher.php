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
     * @throws \Throwable
     */
    public function dispatch($action)
    {
        $this->logger->debug("[ActionDispatcher] Going to dispatch action " . $action->getName() . " whose content is : " . json_encode($action->getProperties()));
        $success = true;
        foreach ($this->pipelineProvider->getCurrentPipeline()->getStores() as $store) {
            if ($store->isTriggeredByAction($action)) {
                $this->logger->debug("  -> store " . $store->getName() . " is triggered by action " . $action->getName());
                try {
                    $this->runStore($store, $action);
                }
                catch (StoreNotRunnableException $e) {
                    $this->logger->error("Can't dispatch action '" . $action->getName() . "'. Message was : " . $e->getMessage());
                    $success = false;
                }
                catch (\Throwable $e) {
                    $trace = "";
                    foreach ($e->getTrace() as $item) {
                        $trace .= "\n" . $item["file"] . ":" . $item["line"];
                    }

                    $this->logger->critical("\n" . 
                        "   An error has been catched while dispatching action " . $action->getName() . "\n" .
                        "   Action parameters were : " . json_encode($action->getProperties()) . "\n" .
                        "   Error message is '" . $e->getMessage() . "'\n" .
                        "   It occured on file " . $e->getFile() . " at line " . $e->getLine() . "\n" .
                        "   Trace is  : $trace\n" .
                        "   Action will be requeued" . "\n" .
                        "====================================================================================="
                    );
                    throw $e;
                }
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
        $this->logger->debug("    -> going to run store " . $store->getName());
        $runner = $this->container->get($store->getService(), ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (!isset($runner)) {
            throw new StoreNotRunnableException("Store " . $store->getName() . " is not runnable : service with name '" . $store->getService() . "' does not exist'");
        }
        if (! $runner instanceof RunnableStore) {
            throw new StoreNotRunnableException("Service '" . $store->getService() . '\' does not implement \Wizbii\PipelineBundle\Runnable\Store interface');
        }

        try {
            $start = microtime(true);
            $eventsGenerator = $runner->run($action);
            $stop = microtime(true);
            $this->logger->info("[" . getmypid() . "]" . " Service " . $store->getService() . " tooked " . ($stop - $start) . " to process " . $action->getName());
            if ($store->hasTriggeredEvent() && isset($eventsGenerator)) {
                foreach ($eventsGenerator->produce() as $dataBag) {
                    $this->eventDispatcher->dispatch($store->getTriggeredEvent()->getName(), $dataBag->all());
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