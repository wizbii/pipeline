<?php

namespace Wizbii\PipelineBundle\Runnable;

abstract class BaseStore implements StoreInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * BaseStore constructor.
     */
    public function __construct()
    {
        $className = explode('\\', get_class($this));
        $this->name = end($className);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}