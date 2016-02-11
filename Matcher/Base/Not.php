<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class Not implements Matcher
{
    public function matches($value)
    {
        if ($this->expectedValue instanceof Matcher) {
            return !$this->expectedValue->matches($value);
        }
        return $value !== $this->expectedValue;
    }

    /**
     * @var mixed|Matcher
     */
    protected $expectedValue;

    /**
     * Not constructor.
     * @param mixed $expectedValue
     */
    public function __construct($expectedValue)
    {
        $this->expectedValue = $expectedValue;
    }

    /**
     * @param mixed|Matcher $expectedValue
     * @return Matcher
     */
    public static function build($expectedValue)
    {
        return new self($expectedValue);
    }
}