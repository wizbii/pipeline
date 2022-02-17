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
        $parts = explode('\\', get_class($this));

        $this->name = end($parts);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
