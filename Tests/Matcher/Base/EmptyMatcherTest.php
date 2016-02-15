<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class EmptyMatcherTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatch()
    {
        $matcher = new EmptyMatcher();
        $this->assertThat($matcher->matches(""), $this->isTrue());
        $this->assertThat($matcher->matches(null), $this->isTrue());
        $this->assertThat($matcher->matches([]), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new EmptyMatcher();
        $this->assertThat($matcher->matches("hello"), $this->isFalse());
        $this->assertThat($matcher->matches(["world"]), $this->isFalse());
    }
}