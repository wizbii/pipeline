<?php

namespace Wizbii\PipelineBundle\Model;

class ActionCreator
{
    /**
     * @var Action
     */
    protected $createdAction;

    /**
     * @var Event[]
     */
    protected $triggeredByEvents = [];

    /**
     * ActionCreator constructor.
     *
     * @param Action $createdAction
     */
    public function __construct($createdAction)
    {
        $this->createdAction = $createdAction;
    }

    /**
     * @return Action
     */
    public function getCreatedAction()
    {
        return $this->createdAction;
    }

    /**
     * @param Action $createdAction
     */
    public function setCreatedAction($createdAction): void
    {
        $this->createdAction = $createdAction;
    }

    /**
     * @return Event[]
     */
    public function getTriggeredByEvents()
    {
        return $this->triggeredByEvents;
    }

    /**
     * @param Event[] $triggeredByEvents
     */
    public function setTriggeredByEvents($triggeredByEvents): void
    {
        $this->triggeredByEvents = $triggeredByEvents;
    }

    /**
     * @param Event $event
     */
    public function addTriggeredByEvent($event): void
    {
        $this->triggeredByEvents[] = $event;
    }

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return Action
     */
    public function buildAction($name, $properties)
    {
        $action = new Action($name);
        $action->setProperties($properties);

        return $action;
    }
}
