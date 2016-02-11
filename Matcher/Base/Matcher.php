<?php

namespace Wizbii\PipelineBundle\Matcher\Base;

interface Matcher
{
    /**
     * @param mixed
     * @return bool
     */
    public function matches($value);
}