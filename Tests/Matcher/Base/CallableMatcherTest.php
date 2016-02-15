<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\CallableMatcher;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class CallableMatcherTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatch()
    {
        $matcher = new CallableMatcher(function($value) {return $value === "foo";});
        $this->assertThat($matcher->matches("foo"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new CallableMatcher(function($value) {return $value === "foo";});
        $this->assertThat($matcher->matches("bar"), $this->isFalse());
    }
}