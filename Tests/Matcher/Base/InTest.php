<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\In;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class InTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatch()
    {
        $matcher = new In(["foo", "bar"]);
        $this->assertThat($matcher->matches("foo"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnNonArray()
    {
        $matcher = new In("foo");
        $this->assertThat($matcher->matches("foo"), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new In(["foo", "bar"]);
        $this->assertThat($matcher->matches(["foo" => "hello"]), $this->isFalse());
    }
}