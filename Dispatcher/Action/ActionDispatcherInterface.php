<?php

namespace Wizbii\PipelineBundle\Dispatcher\Action;

use Wizbii\PipelineBundle\Model\Action;

interface ActionDispatcherInterface
{
    /**
     * @param Action $action the action to be dispatched
     * @return boolean
     */
    public function dispatch($action);
}