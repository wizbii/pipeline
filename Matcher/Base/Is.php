<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class Is implements Matcher
{
    public function matches($value)
    {
        if ($this->expectedValue instanceof Matcher) {
            return $this->expectedValue->matches($value);
        }

        return $value === $this->expectedValue;
    }

    /**
     * @var mixed|Matcher
     */
    protected $expectedValue;

    /**
     * Is constructor.
     *
     * @param mixed $expectedValue
     */
    public function __construct($expectedValue)
    {
        $this->expectedValue = $expectedValue;
    }

    /**
     * @param mixed|Matcher $expectedValue
     */
    public function setExpectedValue($expectedValue): void
    {
        $this->expectedValue = $expectedValue;
    }

    /**
     * @param mixed|Matcher $expectedValue
     *
     * @return self
     */
    public static function build($expectedValue = null)
    {
        return new self($expectedValue);
    }
}
