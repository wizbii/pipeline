<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

interface Matcher
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value);
}
