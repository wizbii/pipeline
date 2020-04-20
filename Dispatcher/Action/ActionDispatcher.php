<?php

namespace Wizbii\PipelineBundle\Dispatcher\Action;

use Psr\Log\LoggerInterface;
use Monolog\ResettableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wizbii\PipelineBundle\Dispatcher\Event\EventDispatcherInterface;
use Wizbii\PipelineBundle\Exception\StoreNotRunnableException;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\DataBag;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Runnable\StoreInterface;
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
        if ($this->logger instanceof ResettableInterface) {
            $this->logger->reset();
        }

        $this->logger->debug("Going to dispatch action", [
            'action_name' => $action->getName(),
            'action_properties' => $action->getProperties(),
        ]);

        $success = true;

        foreach ($this->pipelineProvider->getCurrentPipeline()->getStores() as $store) {
            if ($store->isTriggeredByAction($action)) {
                $this->logger->debug('Store triggered', [
                    'action_name' => $action->getName(),
                    'store_name' => $store->getName(),
                ]);

                try {
                    $this->runStore($store, $action);
                } catch (StoreNotRunnableException $e) {
                    $this->logger->error('Store is not runnable', [
                        'action_name' => $action->getName(),
                        'store_name' => $store->getName(),
                        'exception' => $e,
                    ]);

                    $success = false;
                } catch (\Throwable $e) {
                    $this->logger->critical('An error has been caught while dispatching action', [
                        'action_name' => $action->getName(),
                        'action_properties' => $action->getProperties(),
                        'store_name' => $store->getName(),
                        'exception' => $e,
                    ]);

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
        $runner = $this->container->get($store->getService(), ContainerInterface::NULL_ON_INVALID_REFERENCE);

        if ($runner === null) {
            throw new StoreNotRunnableException(sprintf(
                "Store '%s' is not runnable: service '%s' does not exist",
                $store->getName(),
                $store->getService()
            ));
        }

        if (! $runner instanceof StoreInterface) {
            throw new StoreNotRunnableException(sprintf(
                "Service %s' does not implement '%s'",
                $store->getService(),
                StoreInterface::class
            ));
        }

        try {
            $start = microtime(true);
            $eventsGenerator = $runner->run($action);
            $stop = microtime(true);

            $this->logger->info('Store executed', [
                'store_name' => $store->getName(),
                'store_service' => $store->getService(),
                'action_name' => $action->getName(),
                'duration_ms' => $stop - $start,
            ]);

            if ($store->hasTriggeredEvent() && isset($eventsGenerator)) {
                foreach ($eventsGenerator->produce() as $dataBag) {
                    $this->eventDispatcher->dispatch($store->getTriggeredEvent()->getName(), $dataBag->all());
                }
            }
        }
        catch (\Exception $e) {
            $this->logger->warning("Store has thrown an exception", [
                'store_name' => $store->getName(),
                'store_service' => $store->getService(),
                'action_name' => $action->getName(),
                'exception' => $e,
            ]);
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