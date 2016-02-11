<?php

namespace Wizbii\PipelineBundle\Tests\Factory;

use Wizbii\PipelineBundle\Factory\PipelineFactory;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\ActionCreator;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Model\Pipeline;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class PipelineFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function buildAutoWiredAction()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipelineFactory = new PipelineFactory();
        $pipelineFactory->buildAction($pipeline, "profile_anniversary");

        // check the number of created actions
        $this->assertThat(count($pipeline->getActions()), $this->equalTo(1));

        // check the action
        $profileAnniversaryAction = $pipeline->getAction("profile_anniversary");
        $this->assertThat($profileAnniversaryAction, $this->isInstanceOf(Action::class));

        // check the action creator
        $profileAnniversaryActionCreator = $pipeline->getActionCreatorFor("profile_anniversary");
        $this->assertThat($profileAnniversaryActionCreator, $this->isInstanceOf(ActionCreator::class));
        $this->assertThat(count($profileAnniversaryActionCreator->getTriggeredByEvents()), $this->equalTo(1));
        $this->assertThat($profileAnniversaryActionCreator->getTriggeredByEvents()[0]->getName(), $this->equalTo("profile_anniversary"));
    }

    /**
     * @test
     */
    public function buildCustomWiredAction()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipelineFactory = new PipelineFactory();
        $pipelineFactory->buildAction($pipeline, "profile_network_updated", ["triggered_by_events" => ["profile_new_connection", "profile_new_school"]]);

        // check the number of created actions
        $this->assertThat(count($pipeline->getActions()), $this->equalTo(1));

        // check the action
        $profileAnniversaryAction = $pipeline->getAction("profile_network_updated");
        $this->assertThat($profileAnniversaryAction, $this->isInstanceOf(Action::class));

        // check the action creator
        $profileAnniversaryActionCreator = $pipeline->getActionCreatorFor("profile_network_updated");
        $this->assertThat($profileAnniversaryActionCreator, $this->isInstanceOf(ActionCreator::class));
        $this->assertThat(count($profileAnniversaryActionCreator->getTriggeredByEvents()), $this->equalTo(2));
        $this->assertThat($profileAnniversaryActionCreator->getTriggeredByEvents()[0]->getName(), $this->equalTo("profile_new_connection"));
        $this->assertThat($profileAnniversaryActionCreator->getTriggeredByEvents()[1]->getName(), $this->equalTo("profile_new_school"));
    }

    /**
     * @test
     */
    public function buildValidStore()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $profileAnniversaryAction = new Action("profile_anniversary");
        $pipeline->addAction($profileAnniversaryAction);
        $profileFirstNameUpdatedAction = new Action("profile_first_name_updated");
        $pipeline->addAction($profileFirstNameUpdatedAction);
        $pipelineFactory = new PipelineFactory();
        $pipelineFactory->buildStore($pipeline, "profile_identity_card", [
            "service" => "wizbii.pipeline.storeprofile_identity_card",
            "triggered_by_actions" => ["profile_anniversary", "profile_first_name_updated"],
            "triggered_by_stores" => ["profile_network"],
            "triggered_event" => "profile_identity_card_updated",
        ]);

        // check the number of created stores
        $this->assertThat(count($pipeline->getStores()), $this->equalTo(1));
        $store = $pipeline->getStore("profile_identity_card");

        // check the actions that are triggering this store
        $this->assertThat(count($store->getTriggeredByActions()), $this->equalTo(2));
        $this->assertThat($store->isTriggeredByAction($profileAnniversaryAction), $this->isTrue());
        $this->assertThat($store->isTriggeredByAction($profileFirstNameUpdatedAction), $this->isTrue());

        // check the stores that are triggering this store
        $this->assertThat(count($store->getTriggeredByStores()), $this->equalTo(1));
        $this->assertThat($store->isTriggeredByStore(new Store("profile_network")), $this->isTrue());

        // check the events that are triggered by this store
        $this->assertThat($store->hasTriggeredEvent(), $this->isTrue());
        $this->assertThat($store->getTriggeredEvent()->getName(), $this->equalTo("profile_identity_card_updated"));
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function buildStoreThatDependsOnUnknownAction()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipelineFactory = new PipelineFactory();
        $pipelineFactory->buildStore($pipeline, "profile_identity_card", [
            "service" => "wizbii.pipeline.storeprofile_identity_card",
            "triggered_by_actions" => ["profile_anniversary", "profile_first_name_updated"],
            "triggered_by_stores" => ["profile_network"],
            "triggered_event" => "profile_identity_card_updated",
        ]);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function buildStoreThatIsNeverTriggered()
    {
        $pipeline = new Pipeline("wizbii_profile");
        $pipelineFactory = new PipelineFactory();
        $pipelineFactory->buildStore($pipeline, "profile_identity_card", [
            "service" => "wizbii.pipeline.storeprofile_identity_card"
        ]);
    }
}