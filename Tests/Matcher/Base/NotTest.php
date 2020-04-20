<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Matcher\Base\Not;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class NotTest extends BaseTestCase
{
    /**
     * @test
     */
    public function matchesOnScalar()
    {
        $matcher = new Not('foo');
        $this->assertThat($matcher->matches('bar'), $this->isTrue());
    }

    /**
     * @test
     */
    public function matchesOnMatcher()
    {
        $matcher = new Not(new EmptyMatcher());
        $this->assertThat($matcher->matches('bar'), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnScalar()
    {
        $matcher = new Not('foo');
        $this->assertThat($matcher->matches('foo'), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatchOnMatcher()
    {
        $matcher = new Not(new EmptyMatcher());
        $this->assertThat($matcher->matches(''), $this->isFalse());
    }
}
