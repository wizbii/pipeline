<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class GreaterThanOrEquals implements Matcher
{
    public function matches($value)
    {
        return $this->minValue <= $value;
    }

    /**
     * @var mixed
     */
    protected $minValue;

    /**
     * ContainsKeys constructor.
     * @param array $minValue
     */
    public function __construct($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @param mixed $minValue
     * @return Matcher
     */
    public static function build($minValue)
    {
        return new self($minValue);
    }
}