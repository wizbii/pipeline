<?php

namespace Wizbii\PipelineBundle\Tests\Service;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Service\Pipeline;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class PipelineTest extends BaseTestCase
{
    /**
     * @test
     */
    public function hasAction()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addAction(new Action("first_name_updated"));
        $this->assertThat($pipeline->hasAction("first_name_updated"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveAction()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addAction(new Action("first_name_updated"));
        $this->assertThat($pipeline->hasAction("new_friend"), $this->isFalse());
    }

    /**
     * @test
     */
    public function hasActionCreator()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addActionCreator(new ActionCreator(new Action("first_name_updated")));
        $this->assertThat($pipeline->hasActionCreatorFor("first_name_updated"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveActionCreator()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addActionCreator(new ActionCreator(new Action("first_name_updated")));
        $this->assertThat($pipeline->hasActionCreatorFor("new_friend"), $this->isFalse());
    }

    /**
     * @test
     */
    public function hasEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasEvent("first_name_updated"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasEvent("new_friend"), $this->isFalse());
    }

    /**
     * @test
     */
    public function hasStore()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addStore(new Store("identity_card"));
        $this->assertThat($pipeline->hasStore("identity_card"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveStore()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addStore(new Store("identity_card"));
        $this->assertThat($pipeline->hasStore("profile_network"), $this->isFalse());
    }
}