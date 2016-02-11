<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class CallableMatcher implements Matcher
{
    public function matches($value)
    {
        if (is_callable($this->callable)) {
            return call_user_func_array($this->callable, [$value]);
        }

        return false;
    }

    /**
     * @var callable
     */
    protected $callable;

    /**
     * CallableMatcher constructor.
     * @param callable $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param callable $callable
     * @return Matcher
     */
    public static function build($callable)
    {
        return new self($callable);
    }
}