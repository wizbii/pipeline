<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class In implements Matcher
{
    public function matches($value)
    {
        if (!is_array($this->acceptedValues)) return false;

        return in_array($value, $this->acceptedValues);
    }

    /**
     * @var array
     */
    protected $acceptedValues;

    /**
     * ContainsKeys constructor.
     * @param array $acceptedValues
     */
    public function __construct($acceptedValues)
    {
        $this->acceptedValues = $acceptedValues;
    }

    /**
     * @param mixed $acceptedValues
     * @return Matcher
     */
    public static function build($acceptedValues)
    {
        return new self($acceptedValues);
    }
}