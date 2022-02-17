<?php

namespace Wizbii\PipelineBundle\Tests\Model;

use Wizbii\PipelineBundle\Model\DataBag;
use Wizbii\PipelineBundle\Tests\BaseTestCase;

class DataBagTest extends BaseTestCase
{
    /**
     * @test
     */
    public function setPriority()
    {
        $bag = new DataBag();
        $bag->setPriority(3);
        $this->assertThat($bag->get(DataBag::OPTION_PRIORITY), $this->equalTo(3));
    }

    /**
     * @test
     * @testWith [-1]
     *           [11]
     */
    public function setPriorityThrows(int $priority)
    {
        $this->expectException(\InvalidArgumentException::class);

        $bag = new DataBag();
        $bag->setPriority($priority);
    }
}
