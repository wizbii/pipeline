<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

class IsArray implements Matcher
{
    public function matches($value)
    {
        return is_array($value);
    }

    /**
     * @return Matcher
     */
    public static function build()
    {
        return new self();
    }
}
