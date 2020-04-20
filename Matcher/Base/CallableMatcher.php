<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class CallableMatcher implements Matcher
{
    public function matches($value)
    {
        return call_user_func_array($this->callable, [$value]);
    }

    /**
     * @var callable
     */
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return Matcher
     */
    public static function build(callable $callable)
    {
        return new self($callable);
    }
}
