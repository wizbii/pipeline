<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\Consumer;

class CommandConsumer extends Consumer
{
    /**
     * @var int
     */
    protected $consumeRetryCount = 0;

    /**
     * @var string
     */
    protected $processTitle;

    public function consume($msgAmount)
    {
        if ($this->processTitle) {
            @cli_set_process_title($this->processTitle);
        }

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

    public function setProcessTitle(string $processTitle)
    {
        $this->processTitle = $processTitle;
    }
}