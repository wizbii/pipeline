<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class EmptyMatcher implements Matcher
{
    public function matches($value)
    {
        return empty($value);
    }

    /**
     * @return Matcher
     */
    public static function build()
    {
        return new self();
    }
}