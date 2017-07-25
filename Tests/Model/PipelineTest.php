<?php

namespace Wizbii\PipelineBundle\Tests\Model;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Event;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Model\Pipeline;
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
    public function hasIncomingEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addIncomingEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasIncomingEvent("first_name_updated"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveIncomingEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addIncomingEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasIncomingEvent("new_friend"), $this->isFalse());
    }

    /**
     * @test
     */
    public function hasOutgoingEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addOutgoingEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasOutgoingEvent("first_name_updated"), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotHaveOutgoingEvent()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipeline->addOutgoingEvent(new Event("first_name_updated"));
        $this->assertThat($pipeline->hasOutgoingEvent("new_friend"), $this->isFalse());
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

    /**
     * @test
     */
    public function doesNotHaveCircularReferences()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $profileIdentityCardStore = new Store("profile_identity_card");
        $profileNetworkStore = new Store("profile_network");
        $profileThanxStore = new Store("profile_thanx");
        $profileIdentityCardStore->addTriggeredByStore($profileNetworkStore);
        $profileIdentityCardStore->addTriggeredByStore($profileThanxStore);
        $pipeline->addStore($profileIdentityCardStore);
        $pipeline->addStore($profileNetworkStore);
        $pipeline->addStore($profileThanxStore);

        $pipeline->checkForCircularReferences();
        $this->assertTrue(true);
    }

    /**
     * @test
     * @expectedException \Wizbii\PipelineBundle\Exception\CircularPipelineException
     */
    public function circularReferencesThrowsException()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $profileIdentityCardStore = new Store("profile_identity_card");
        $profileNetworkStore = new Store("profile_network");
        $profileThanxStore = new Store("profile_thanx");
        $profileIdentityCardStore->addTriggeredByStore($profileNetworkStore);
        $profileNetworkStore->addTriggeredByStore($profileThanxStore);
        $profileThanxStore->addTriggeredByStore($profileIdentityCardStore);
        $pipeline->addStore($profileIdentityCardStore);
        $pipeline->addStore($profileNetworkStore);
        $pipeline->addStore($profileThanxStore);

        $pipeline->checkForCircularReferences();
    }
}