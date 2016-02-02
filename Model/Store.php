<?php

namespace Wizbii\PipelineBundle\Model;

class Store
{
    const ASYNCHRONOUS = "async";
    const IMMEDIATE    = "immediate";

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var Store[]
     */
    protected $triggeredByStores = [];

    /**
     * @var Action[]
     */
    protected $triggeredByActions = [];

    /**
     * @var Event[]
     */
    protected $triggeredEvents = [];

    /**
     * Store constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isNeverTriggered()
    {
        return empty($this->triggeredByActions) && empty($this->triggeredByStores);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return Store[]
     */
    public function getTriggeredByStores()
    {
        return $this->triggeredByStores;
    }

    /**
     * @param Store[] $triggeredByStores
     */
    public function setTriggeredByStores($triggeredByStores)
    {
        $this->triggeredByStores = $triggeredByStores;
    }

    /**
     * @param Store $store
     */
    public function addTriggeredByStore($store)
    {
        $this->triggeredByStores[$store->getName()] = $store;
    }

    /**
     * @param Store $store
     * @return bool
     */
    public function isTriggeredByStore($store)
    {
        return isset($store) && is_array($this->triggeredByStores) && array_key_exists($store->getName(), $this->triggeredByStores);
    }

    /**
     * @param Store $store
     * @return bool
     */
    public function dependsOnStore($store)
    {
        foreach ($this->triggeredByStores as $triggeredByStore) {
            if ($triggeredByStore->getName() === $store->getName()) return true;
            if ($triggeredByStore->dependsOnStore($store)) return true;
        }

        return false;
    }

    /**
     * @return Action[]
     */
    public function getTriggeredByActions()
    {
        return $this->triggeredByActions;
    }

    /**
     * @param Action[] $triggeredByActions
     */
    public function setTriggeredByActions($triggeredByActions)
    {
        $this->triggeredByActions = $triggeredByActions;
    }

    /**
     * @param Action $action
     */
    public function addTriggeredByAction($action)
    {
        $this->triggeredByActions[$action->getName()] = $action;
    }

    /**
     * @param Action $action
     * @return bool
     */
    public function isTriggeredByAction($action)
    {
        return isset($action) && is_array($this->triggeredByActions) && array_key_exists($action->getName(), $this->triggeredByActions);
    }

    /**
     * @return Event[]
     */
    public function getTriggeredEvents()
    {
        return $this->triggeredEvents;
    }

    /**
     * @param Event[] $triggeredEvents
     */
    public function setTriggeredEvents($triggeredEvents)
    {
        $this->triggeredEvents = $triggeredEvents;
    }

    /**
     * @param Event $event
     */
    public function addTriggeredEvent($event)
    {
        $this->triggeredEvents[] = $event;
    }
}