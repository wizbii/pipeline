<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class AndMatcher implements Matcher
{
    public function matches($value)
    {
        foreach ($this->matchers as $matcher) {
            if (!$matcher->matches($value)) return false;
        }

        return true;
    }

    /**
     * @var Matcher[]
     */
    protected $matchers = [];

    /**
     * @param Matcher $matcher
     * @return $this
     */
    public function addMatcher($matcher)
    {
        $this->matchers[] = $matcher;

        return $this;
    }

    /**
     * @return AndMatcher
     */
    public static function build()
    {
        return new self();
    }
}