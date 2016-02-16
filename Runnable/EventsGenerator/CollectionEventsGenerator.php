<?php

namespace Wizbii\PipelineBundle\Runnable\EventsGenerator;

use Wizbii\PipelineBundle\Model\DataBag;

class CollectionEventsGenerator implements EventsGenerator
{
    public function produce()
    {
        foreach ($this->dataBags as $dataBag) {
            yield $dataBag;
        }
    }

    /**
     * @param DataBag[] $dataBags
     */
    public function setDataBags($dataBags)
    {
        $this->dataBags = $dataBags;
    }

    /**
     * @param DataBag $dataBag
     */
    public function addDataBag($dataBag)
    {
        $this->dataBags[] = $dataBag;
    }

    /**
     * CollectionEventsGenerator constructor.
     * @param DataBag[] $dataBags
     */
    public function __construct($dataBags = [])
    {
        $this->dataBags = $dataBags;
    }

    /**
     * @var DataBag[]
     */
    protected $dataBags = [];
}