<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class LessThanOrEquals implements Matcher
{
    public function matches($value)
    {
        return $this->maxValue >= $value;
    }

    /**
     * @var mixed
     */
    protected $maxValue;

    /**
     * ContainsKeys constructor.
     *
     * @param mixed $maxValue
     */
    public function __construct($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @param mixed $maxValue
     *
     * @return Matcher
     */
    public static function build($maxValue)
    {
        return new self($maxValue);
    }
}
