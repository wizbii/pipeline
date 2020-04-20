<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\GreaterThanOrEquals;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class GreaterThanOrEqualsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatchOnNumeric()
    {
        $matcher = new GreaterThanOrEquals(1);
        $this->assertThat($matcher->matches(1), $this->isTrue());
        $this->assertThat($matcher->matches(2), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchOnString()
    {
        $matcher = new GreaterThanOrEquals('a');
        $this->assertThat($matcher->matches('a'), $this->isTrue());
        $this->assertThat($matcher->matches('b'), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new GreaterThanOrEquals(1);
        $this->assertThat($matcher->matches(0), $this->isFalse());
    }
}
