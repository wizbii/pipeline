<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\AndMatcher;
use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Matcher\Base\In;
use Wizbii\PipelineBundle\Matcher\Base\Is;
use Wizbii\PipelineBundle\Matcher\Base\Not;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class AndMatcherTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatchWithNoOperand()
    {
        $matcher = new AndMatcher();
        $this->assertThat($matcher->matches("any value"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchWithSingleOperand()
    {
        $matcher = new AndMatcher();
        $matcher->addMatcher(new Is("foo"));
        $this->assertThat($matcher->matches("foo"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchWithMultipleOperands()
    {
        $matcher = new AndMatcher();
        $matcher->addMatcher(new Is("foo"));
        $matcher->addMatcher(new Is(new Not(new EmptyMatcher())));
        $matcher->addMatcher(new In(["foo", "bar"]));
        $this->assertThat($matcher->matches("foo"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new AndMatcher();
        $matcher->addMatcher(new Is("foo"));
        $this->assertThat($matcher->matches("bar"), $this->isFalse());
    }
}