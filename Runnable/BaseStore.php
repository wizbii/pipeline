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
        $fqcn = get_class($this);
        $parts = explode('\\', get_class($this));

        $this->name = $parts ? end($parts) : $fqcn;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
