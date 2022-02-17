<?php

namespace Wizbii\PipelineBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\MultipleConsumer as BaseMultipleConsumer;

/**
 * Multiple consumer that declare an exchange per queue it consumes.
 */
final class MultipleConsumer extends BaseMultipleConsumer
{
    public function addQueue(array $queueOptions): void
    {
        $this->queues[$queueOptions['name']] = array_merge($this->queueOptions, $queueOptions);
    }

    protected function exchangeDeclare(): void
    {
        foreach ($this->queues as $name => $options) {
            if ($this->exchangeOptions['declare']) {
                $this->getChannel()->exchange_declare(
                    $name,
                    $this->exchangeOptions['type'] ?? 'direct',
                    $this->exchangeOptions['passive'],
                    $this->exchangeOptions['durable'],
                    $this->exchangeOptions['auto_delete'],
                    $this->exchangeOptions['internal'],
                    $this->exchangeOptions['nowait'],
                    $this->exchangeOptions['arguments'],
                    $this->exchangeOptions['ticket'] ?? null);

                $this->exchangeDeclared = true;
            }
        }
    }

    protected function queueDeclare(): void
    {
        foreach ($this->queues as $name => $options) {
            $result = $this->getChannel()->queue_declare(
                $name,
                $options['passive'] ?? false,
                $options['durable'] ?? false,
                $options['exclusive'] ?? false,
                $options['auto_delete'] ?? true,
                $options['nowait'] ?? false,
                $options['arguments'] ?? [],
                $options['ticket'] ?? null
            );

            if ($result === null) {
                $queueName = $name;
            } else {
                $queueName = $result[0];
            }

            if (isset($options['routing_keys']) && count($options['routing_keys']) > 0) {
                foreach ($options['routing_keys'] as $routingKey) {
                    $this->getChannel()->queue_bind($queueName, $name, $routingKey);
                }
            } else {
                $this->getChannel()->queue_bind($queueName, $name, $this->routingKey);
            }
        }

        $this->queueDeclared = true;
    }
}
