<?php

namespace Wizbii\PipelineBundle\Tests\Model;

use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class ActionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function hasProperty()
    {
        $action = new Action('profile_updated');
        $action->addProperty('profile_slug', 'benjamin_ducousso');
        $this->assertThat($action->hasProperty('profile_slug'), $this->isTrue());
        $this->assertThat($action->getProperty('profile_slug'), $this->equalTo('benjamin_ducousso'));
    }

    /**
     * @test
     */
    public function doesNotHaveProperty()
    {
        $action = new Action('profile_updated');
        $this->assertThat($action->hasProperty('profile_slug'), $this->isFalse());
        $this->assertThat($action->getProperty('profile_slug'), $this->isNull());
        $this->assertThat($action->getProperty('profile_slug', 'defaultValue'), $this->equalTo('defaultValue'));
    }
}
