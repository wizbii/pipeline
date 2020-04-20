<?php

namespace Wizbii\PipelineBundle\Runnable;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Runnable\EventsGenerator\EventsGenerator;

interface StoreInterface
{
    /**
     * @param Action $action
     *
     * @return EventsGenerator|null
     */
    public function run($action);

    /**
     * @return string
     */
    public function getName();
}
