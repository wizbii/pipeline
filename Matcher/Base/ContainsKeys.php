<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class ContainsKeys implements Matcher
{
    public function matches($value)
    {
        if (!is_array($this->expectedKeys)) return false;
        if (!is_array($value)) return false;
        foreach ($this->expectedKeys as $key) {
            if (!array_key_exists($key, $value)) return false;
        }

        return true;
    }

    /**
     * @var array
     */
    protected $expectedKeys;

    /**
     * ContainsKeys constructor.
     * @param array $expectedKeys
     */
    public function __construct($expectedKeys)
    {
        $this->expectedKeys = $expectedKeys;
    }

    /**
     * @param mixed|Matcher $expectedKeys
     * @return Matcher
     */
    public static function build($expectedKeys)
    {
        return new self($expectedKeys);
    }
}