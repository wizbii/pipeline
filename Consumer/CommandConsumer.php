<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\Consumer;

class CommandConsumer extends Consumer
{
    /**
     * @var int
     */
    protected $consumeRetryCount = 0;

    public function consume($msgAmount)
    {
        try {
            parent::consume($msgAmount);
            $this->consumeRetryCount = 0;
        }
        catch (\Exception $e) {
            $this->consumeRetryCount++;
            if ($this->consumeRetryCount < 10) {
                parent::consume($msgAmount);
                return;
            }
            throw $e;
        }
    }

}