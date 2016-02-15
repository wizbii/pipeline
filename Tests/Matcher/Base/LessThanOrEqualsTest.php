<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\LessThanOrEquals;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class LessThanOrEqualsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatchOnNumeric()
    {
        $matcher = new LessThanOrEquals(1);
        $this->assertThat($matcher->matches(1), $this->isTrue());
        $this->assertThat($matcher->matches(0), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchOnString()
    {
        $matcher = new LessThanOrEquals("b");
        $this->assertThat($matcher->matches("b"), $this->isTrue());
        $this->assertThat($matcher->matches("a"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new LessThanOrEquals(1);
        $this->assertThat($matcher->matches(2), $this->isFalse());
    }
}