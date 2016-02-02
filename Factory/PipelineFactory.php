<?php

namespace Wizbii\PipelineBundle\Factory;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Service\Pipeline;
use Wizbii\PipelineBundle\Model\Store;

class PipelineFactory
{
    public function getPipeline($configuration)
    {
        $pipeline = new Pipeline();

        // create Actions, Events and ActionCreators
        foreach ($configuration["actions"] as $actionName => $actionConfig) {
            $this->buildAction($pipeline, $actionName, $actionConfig);
        }

        // create Stores
        $aStores = $configuration["stores"];
        foreach ($aStores as $storeName => $storeConfig) {
            $this->buildStore($pipeline, $storeName, $storeConfig);
        }

        // validate stores to ensure there is no circular references between them
        $this->checkForCircularReferences($pipeline);

        return $pipeline;
    }

    /**
     * Add actions inside Pipeline based on the configuration provided by Symfony Configuration
     * @param Pipeline $pipeline
     * @param string $actionName
     * @param array $actionConfiguration
     */
    public function buildAction($pipeline, $actionName, $actionConfiguration = [])
    {
        $triggeredByEvents = is_array($actionConfiguration) && array_key_exists("triggered_by_events", $actionConfiguration) ? $actionConfiguration["triggered_by_events"] : [];

        if (empty($triggeredByEvents)) {
            // default behavior : auto-wiring action to the same event name
            $triggeredByEvents = [$actionName];
        }

        // create the action and the action creator
        $action = new Action($actionName);
        $actionCreator = new ActionCreator($action);
        foreach ($triggeredByEvents as $triggeredByEvent) {
            $event = $pipeline->hasEvent($triggeredByEvent) ? $pipeline->getEvent($triggeredByEvent) : new Event($triggeredByEvent);
            $actionCreator->addTriggeredByEvent($event);
            $pipeline->addEvent($event);
        }
        $pipeline->addAction($action);
        $pipeline->addActionCreator($actionCreator);
    }

    /**
     * @param Pipeline $pipeline
     * @param string $storeName
     * @param array $storeConfiguration
     * @throws InvalidConfigurationException
     */
    public function buildStore($pipeline, $storeName, $storeConfiguration = [])
    {
        $triggeredByActions = is_array($storeConfiguration) && array_key_exists("triggered_by_actions", $storeConfiguration) ? $storeConfiguration["triggered_by_actions"] : [];
        $triggeredByStores = is_array($storeConfiguration) && array_key_exists("triggered_by_stores", $storeConfiguration) ? $storeConfiguration["triggered_by_stores"] : [];
        $triggeredEvents = is_array($storeConfiguration) && array_key_exists("triggered_events", $storeConfiguration) ? $storeConfiguration["triggered_events"] : [];
        $service = is_array($storeConfiguration) && array_key_exists("service", $storeConfiguration) ? $storeConfiguration["service"] : null;

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
        foreach ($triggeredEvents as $triggeredEvent) {
            $event = $pipeline->hasEvent($triggeredEvent) ? $pipeline->getEvent($triggeredEvent) : new Event($triggeredEvent);
            $store->addTriggeredEvent($event);
        }
        $pipeline->addStore($store);
    }

    /**
     * @param Pipeline $pipeline
     * @throws InvalidConfigurationException
     */
    public function checkForCircularReferences($pipeline)
    {
        foreach ($pipeline->getStores() as $storeName => $store) {
            if ($store->dependsOnStore($store)) {
                throw new InvalidConfigurationException("Store $storeName depends on itself");
            }
        }
    }
}