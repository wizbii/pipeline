<?php

namespace Wizbii\PipelineBundle\Factory;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Wizbii\PipelineBundle\Exception\CircularPipelineException;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Model\Pipeline;
use Wizbii\PipelineBundle\Model\Store;

class PipelineFactory
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @return Pipeline
     *
     * @throws CircularPipelineException
     */
    public function buildPipeline($configuration)
    {
        $pipeline = new Pipeline();

        // set pipeline name
        $this->buildName($pipeline, $configuration);

        // create Actions, Events and ActionCreators
        foreach ($configuration['actions'] as $actionName => $actionConfig) {
            $this->buildAction($pipeline, $actionName, $actionConfig);
        }

        // create Stores
        $aStores = $configuration['stores'];
        foreach ($aStores as $storeName => $storeConfig) {
            $this->buildStore($pipeline, $storeName, $storeConfig);
        }

        // validate stores to ensure there is no circular references between them
        $pipeline->checkForCircularReferences();

        return $pipeline;
    }

    /**
     * Set the pipeline name. Empty name will throw an exception.
     *
     * @param Pipeline $pipeline
     * @param array    $configuration
     *
     * @throws InvalidConfigurationException
     */
    public function buildName($pipeline, $configuration): void
    {
        $name = is_array($configuration) && array_key_exists('name', $configuration) ? $configuration['name'] : null;
        if (empty($name)) {
            throw new InvalidConfigurationException('Missing pipeline name');
        }
        $pipeline->setName($name);
    }

    /**
     * Add actions inside Pipeline based on the configuration provided by Symfony Configuration.
     *
     * @param Pipeline $pipeline
     * @param string   $actionName
     * @param array    $actionConfiguration
     */
    public function buildAction($pipeline, $actionName, $actionConfiguration = []): void
    {
        $triggeredByEvents = is_array($actionConfiguration) && array_key_exists('triggered_by_events', $actionConfiguration) ? $actionConfiguration['triggered_by_events'] : [];

        if (empty($triggeredByEvents)) {
            // default behavior : auto-wiring action to the same event name
            $triggeredByEvents = [$actionName];
        }

        // create the action and the action creator
        $action = new Action($actionName);
        $actionCreator = new ActionCreator($action);
        foreach ($triggeredByEvents as $triggeredByEvent) {
            $event = $pipeline->getIncomingEvent($triggeredByEvent) ?: new Event($triggeredByEvent);
            $actionCreator->addTriggeredByEvent($event);
            $pipeline->addIncomingEvent($event);
        }
        $pipeline->addAction($action);
        $pipeline->addActionCreator($actionCreator);
    }

    /**
     * @param Pipeline $pipeline
     * @param string   $storeName
     * @param array    $storeConfiguration
     *
     * @throws InvalidConfigurationException
     */
    public function buildStore($pipeline, $storeName, $storeConfiguration = []): void
    {
        $triggeredByActions = is_array($storeConfiguration) && array_key_exists('triggered_by_actions', $storeConfiguration) ? $storeConfiguration['triggered_by_actions'] : [];
        $triggeredByStores = is_array($storeConfiguration) && array_key_exists('triggered_by_stores', $storeConfiguration) ? $storeConfiguration['triggered_by_stores'] : [];
        $triggeredEvent = is_array($storeConfiguration) && array_key_exists('triggered_event', $storeConfiguration) ? $storeConfiguration['triggered_event'] : null;
        $service = is_array($storeConfiguration) && array_key_exists('service', $storeConfiguration) ? $storeConfiguration['service'] : null;

        $store = $pipeline->hasStore($storeName) ? $pipeline->getStore($storeName) : new Store($storeName);
        $store->setService($service);
        foreach ($triggeredByActions as $triggeredByAction) {
            if (!$pipeline->hasAction($triggeredByAction)) {
                throw new InvalidConfigurationException("Store $storeName depends on an unknown action : $triggeredByAction");
            }
            $store->addTriggeredByAction($pipeline->getAction($triggeredByAction));
        }
        foreach ($triggeredByStores as $storeName) {
            $triggeredByStore = new Store($storeName);
            if ($pipeline->hasStore($storeName)) {
                $triggeredByStore = $pipeline->getStore($storeName);
            }
            $store->addTriggeredByStore($triggeredByStore);
        }
        // check that store is triggered by an action or another store.
        if ($store->isNeverTriggered()) {
            throw new InvalidConfigurationException("Store $storeName is never triggered");
        }

        // add triggered event
        if (isset($triggeredEvent)) {
            $event = $pipeline->getOutgoingEvent($triggeredEvent) ?: new Event($triggeredEvent);
            $store->setTriggeredEvent($event);
            $pipeline->addOutgoingEvent($event);
        }
        $pipeline->addStore($store);
    }
}
