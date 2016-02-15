<?php

namespace Wizbii\PipelineBundle\Tests\Matcher;

use Wizbii\PipelineBundle\Matcher\ActionMatcher;
use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Matcher\Base\Is;
use Wizbii\PipelineBundle\Matcher\Base\Not;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class ActionMatcherTest extends BaseTestCase
{
    /**
     * @test
     */
    public function doesMatchOnActionName()
    {
        $actionMatcher = new ActionMatcher();
        $actionMatcher->addMatcherOnActionName()->addMatcher(new Is("foo"));

        $this->assertThat($actionMatcher->matches(new Action("foo")), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatchOnPropertyNames()
    {
        $actionMatcher = new ActionMatcher();
        $actionMatcher->addMatcherOnPropertyName("hello")->addMatcher(new Is("world"));
        $action = new Action("foo");
        $action->addProperty("hello", "world");

        $this->assertThat($actionMatcher->matches($action), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesMatch()
    {
        $actionMatcher = new ActionMatcher();
        $actionMatcher->addMatcherOnPropertyName("foo");
        $actionMatcher->addMatcherOnPropertyName("hello")->addMatcher(new Is("world"));
        $actionMatcher->addMatcherOnPropertyName("profile_id")->addMatcher(new Not(new EmptyMatcher()));
        $action = new Action("foo");
        $action->addProperty("hello", "world");
        $action->addProperty("profile_id", "john");

        $this->assertThat($actionMatcher->matches($action), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotMatchOnActionName()
    {
        $actionMatcher = new ActionMatcher();
        $actionMatcher->addMatcherOnActionName()->addMatcher(new Is("foo"));

        $this->assertThat($actionMatcher->matches(new Action("bar")), $this->isFalse());
    }

    /**
     * @test
     */
    public function doesNotMatchOnPropertyName()
    {
        $actionMatcher = new ActionMatcher();
        $actionMatcher->addMatcherOnPropertyName("hello")->addMatcher(new Is("world"));
        $action = new Action("foo");
        $action->addProperty("hello", "john");

        $this->assertThat($actionMatcher->matches($action), $this->isFalse());
    }
}