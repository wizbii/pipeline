<?php

namespace Wizbii\PipelineBundle\Runnable;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\DataBag;

interface StoreInterface
{
    /**
     * @param Action $action
     * @return DataBag[]
     */
    public function run($action);

    /**
     * @return string
     */
    public function getName();
}