<?php

namespace Wizbii\PipelineBundle\Tests\Model;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\Store;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class StoreTest extends BaseTestCase
{
    /**
     * @test
     */
    public function isNeverTriggered()
    {
        $store = new Store("identity_card");
        $this->assertThat($store->isNeverTriggered(), $this->isTrue());
    }

    /**
     * @test
     */
    public function isTriggeredByAction()
    {
        $store = new Store("identity_card");
        $action = new Action("first_name_updated");
        $store->addTriggeredByAction($action);
        $this->assertThat($store->isNeverTriggered(), $this->isFalse());
        $this->assertThat($store->isTriggeredByAction($action), $this->isTrue());
    }

    /**
     * @test
     */
    public function isTriggeredByStore()
    {
        $store = new Store("identity_card");
        $anotherStore = new Store("profile_network");
        $store->addTriggeredByStore($anotherStore);
        $this->assertThat($store->isNeverTriggered(), $this->isFalse());
        $this->assertThat($store->isTriggeredByStore($anotherStore), $this->isTrue());
    }

    /**
     * @test
     */
    public function dependsOnStore()
    {
        $store = new Store("identity_card");
        $aSecondStore = new Store("profile_network");
        $aThirdStore = new Store("profile_friends");
        $store->addTriggeredByStore($aSecondStore);
        $aSecondStore->addTriggeredByStore($aThirdStore);
        // aThirdStore => aSecondStore => store
        $this->assertThat($store->dependsOnStore($aThirdStore), $this->isTrue());
    }

    /**
     * @test
     */
    public function doesNotDependOnStore()
    {
        $store = new Store("identity_card");
        $aSecondStore = new Store("profile_network");
        $aThirdStore = new Store("profile_friends");
        $store->addTriggeredByStore($aSecondStore);
        // aSecondStore => store but aThirdStore is not wired
        $this->assertThat($store->dependsOnStore($aThirdStore), $this->isFalse());
    }
}