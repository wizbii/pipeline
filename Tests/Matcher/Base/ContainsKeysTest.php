<?php

namespace Wizbii\PipelineBundle\Tests\Matcher\Base;

use Wizbii\PipelineBundle\Matcher\Base\ContainsKeys;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class ContainsKeysTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatch()
    {
        $matcher = new ContainsKeys(["foo", "bar"]);
        $this->assertThat($matcher->matches(["foo" => "hello", "bar" => "world"]), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnNonArray()
    {
        $matcher = new ContainsKeys(["foo", "bar"]);
        $this->assertThat($matcher->matches("hello"), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatch()
    {
        $matcher = new ContainsKeys(["foo", "bar"]);
        $this->assertThat($matcher->matches(["foo" => "hello"]), $this->isFalse());
    }
}