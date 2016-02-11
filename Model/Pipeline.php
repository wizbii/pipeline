<?php

namespace Wizbii\PipelineBundle\Model;

use Wizbii\PipelineBundle\Exception\CircularPipelineException;

class Pipeline
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var Event[]
     */
    protected $incomingEvents = [];

    /**
     * @var Event[]
     */
    protected $outgoingEvents = [];

    /**
     * @var ActionCreator[]
     */
    protected $actionCreators = [];

    /**
     * @var Store[]
     */
    protected $stores = [];

    /**
     * @throws CircularPipelineException
     */
    public function checkForCircularReferences()
    {
        foreach ($this->stores as $storeName => $store) {
            if ($store->dependsOnStore($store)) {
                throw new CircularPipelineException("Store $storeName depends on itself");
            }
        }
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
    public function getIncomingEvents()
    {
        return $this->incomingEvents;
    }

    /**
     * @param Event[] $incomingEvents
     */
    public function setIncomingEvents($incomingEvents)
    {
        $this->incomingEvents = $incomingEvents;
    }

    /**
     * @param Event $event
     */
    public function addIncomingEvent($event)
    {
        $this->incomingEvents[$event->getName()] = $event;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function getIncomingEvent($name)
    {
        return $this->hasIncomingEvent($name) ? $this->incomingEvents[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasIncomingEvent($name)
    {
        return is_array($this->incomingEvents) && array_key_exists($name, $this->incomingEvents);
    }

    /**
     * @return Event[]
     */
    public function getOutgoingEvents()
    {
        return $this->outgoingEvents;
    }

    /**
     * @param Event[] $outgoingEvents
     */
    public function setOutgoingEvents($outgoingEvents)
    {
        $this->outgoingEvents = $outgoingEvents;
    }

    /**
     * @param Event $event
     */
    public function addOutgoingEvent($event)
    {
        $this->outgoingEvents[$event->getName()] = $event;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function getOutgoingEvent($name)
    {
        return $this->hasOutgoingEvent($name) ? $this->outgoingEvents[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOutgoingEvent($name)
    {
        return is_array($this->outgoingEvents) && array_key_exists($name, $this->outgoingEvents);
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