<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\IsArray;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class IsArrayTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatchOnEmptyArray()
    {
        $matcher = new IsArray();
        $this->assertThat($matcher->matches([]), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchOnNonEmptyArray()
    {
        $matcher = new IsArray();
        $this->assertThat($matcher->matches(["foo", "bar" => "baz"]), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnScalar()
    {
        $matcher = new IsArray();
        $this->assertThat($matcher->matches("bar"), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatchOnNull()
    {
        $matcher = new IsArray();
        $this->assertThat($matcher->matches(null), $this->isFalse());
    }
}