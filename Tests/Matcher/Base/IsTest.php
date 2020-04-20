<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Matcher\Base\Is;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class IsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function matchesOnScalar()
    {
        $matcher = new Is('foo');
        $this->assertThat($matcher->matches('foo'), $this->isTrue());
    }

    /**
     * @test
     */
    public function matchesOnMatcher()
    {
        $matcher = new Is(new EmptyMatcher());
        $this->assertThat($matcher->matches(''), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnScalar()
    {
        $matcher = new Is('foo');
        $this->assertThat($matcher->matches('bar'), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatchOnMatcher()
    {
        $matcher = new Is(new EmptyMatcher());
        $this->assertThat($matcher->matches('any non empty value'), $this->isFalse());
    }
}
