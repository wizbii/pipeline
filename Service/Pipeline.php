<?php

namespace Wizbii\PipelineBundle\Service;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Model\Store;

class Pipeline
{
    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var Event[]
     */
    protected $events = [];

    /**
     * @var ActionCreator[]
     */
    protected $actionCreators = [];

    /**
     * @var Store[]
     */
    protected $stores = [];

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param Action $action
     */
    public function addAction($action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @param string $name
     * @return Action
     */
    public function getAction($name)
    {
        return $this->hasAction($name) ? $this->actions[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAction($name)
    {
        return is_array($this->actions) && array_key_exists($name, $this->actions);
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event[] $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * @param Event $event
     */
    public function addEvent($event)
    {
        $this->events[$event->getName()] = $event;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function getEvent($name)
    {
        return $this->hasEvent($name) ? $this->events[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasEvent($name)
    {
        return is_array($this->events) && array_key_exists($name, $this->events);
    }

    /**
     * @return ActionCreator[]
     */
    public function getActionCreators()
    {
        return $this->actionCreators;
    }

    /**
     * @param ActionCreator[] $actionCreators
     */
    public function setActionCreators($actionCreators)
    {
        $this->actionCreators = $actionCreators;
    }

    /**
     * @param ActionCreator $actionCreator
     */
    public function addActionCreator($actionCreator)
    {
        $this->actionCreators[$actionCreator->getCreatedAction()->getName()] = $actionCreator;
    }

    /**
     * @param string $name
     * @return ActionCreator
     */
    public function getActionCreatorFor($name)
    {
        return $this->hasActionCreatorFor($name) ? $this->actionCreators[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasActionCreatorFor($name)
    {
        return is_array($this->actionCreators) && array_key_exists($name, $this->actionCreators);
    }

    /**
     * @return Store[]
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @param Store[] $stores
     */
    public function setStores($stores)
    {
        $this->stores = $stores;
    }

    /**
     * @param Store $store
     */
    public function addStore($store)
    {
        $this->stores[$store->getName()] = $store;
    }

    /**
     * @param string $name
     * @return Store
     */
    public function getStore($name)
    {
        return $this->hasStore($name) ? $this->stores[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasStore($name)
    {
        return is_array($this->stores) && array_key_exists($name, $this->stores);
    }
}