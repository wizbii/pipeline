<?php

namespace Wizbii\PipelineBundle\Model;

class Store
{
    public const ASYNCHRONOUS = 'async';
    public const IMMEDIATE = 'immediate';

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
     * @var Event
     */
    protected $triggeredEvent;

    /**
     * Store constructor.
     *
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
    public function setName($name): void
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
    public function setService($service): void
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
    public function setTriggeredByStores($triggeredByStores): void
    {
        $this->triggeredByStores = $triggeredByStores;
    }

    /**
     * @param Store $store
     */
    public function addTriggeredByStore($store): void
    {
        $this->triggeredByStores[$store->getName()] = $store;
    }

    /**
     * @param Store|null $store
     *
     * @return bool
     */
    public function isTriggeredByStore($store)
    {
        return isset($store) && is_array($this->triggeredByStores) && array_key_exists($store->getName(), $this->triggeredByStores);
    }

    /**
     * @param Store $store
     *
     * @return bool
     */
    public function dependsOnStore($store)
    {
        foreach ($this->triggeredByStores as $triggeredByStore) {
            if ($triggeredByStore->getName() === $store->getName()) {
                return true;
            }
            if ($triggeredByStore->dependsOnStore($store)) {
                return true;
            }
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
    public function setTriggeredByActions($triggeredByActions): void
    {
        $this->triggeredByActions = $triggeredByActions;
    }

    /**
     * @param Action $action
     */
    public function addTriggeredByAction($action): void
    {
        $this->triggeredByActions[$action->getName()] = $action;
    }

    /**
     * @param Action|null $action
     *
     * @return bool
     */
    public function isTriggeredByAction($action)
    {
        return isset($action) && is_array($this->triggeredByActions) && array_key_exists($action->getName(), $this->triggeredByActions);
    }

    /**
     * @return Event
     */
    public function getTriggeredEvent()
    {
        return $this->triggeredEvent;
    }

    /**
     * @param Event $triggeredEvent
     */
    public function setTriggeredEvent($triggeredEvent): void
    {
        $this->triggeredEvent = $triggeredEvent;
    }

    /**
     * @return bool
     */
    public function hasTriggeredEvent()
    {
        return !empty($this->triggeredEvent);
    }
}
