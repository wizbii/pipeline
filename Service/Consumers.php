<?php

namespace Wizbii\PipelineBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\Consumer;

class Consumers implements \IteratorAggregate, \Countable
{
    /**
     * Parameter storage.
     *
     * @var Consumer[]
     */
    protected $consumers;

    /**
     * Constructor.
     *
     * @param Consumer[] $consumers An array of Consumer
     */
    public function __construct(array $consumers = array())
    {
        $this->consumers = $consumers;
    }

    /**
     * Returns the $consumers.
     *
     * @return Consumer[] An array of consumers
     */
    public function all()
    {
        return $this->consumers;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of Consumer keys
     */
    public function keys()
    {
        return array_keys($this->consumers);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param Consumer[] $consumers An array of consumers
     */
    public function replace(array $consumers = array())
    {
        $this->consumers = $consumers;
    }

    /**
     * Adds consumers.
     *
     * @param Consumer[] $consumers An array of consumers
     */
    public function add(array $consumers = array())
    {
        $this->consumers = array_replace($this->consumers, $consumers);
    }

    /**
     * Returns a Consumer by name.
     * @param string $key The key
     * @param Consumer $default The default value
     *
     * @return Consumer
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->consumers) ? $this->consumers[$key] : $default;
    }

    /**
     * Sets a Consumer by name.
     *
     * @param string $key   The key
     * @param Consumer[]  $consumer The value
     */
    public function set($key, $consumer)
    {
        $this->consumers[$key] = $consumer;
    }

    /**
     * Returns true if the Consumer is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the Consumer exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->consumers);
    }

    /**
     * Removes a Consumer.
     *
     * @param string $key The key
     */
    public function remove($key)
    {
        unset($this->consumers[$key]);
    }

    /**
     * Returns an iterator for Consumers.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->consumers);
    }

    /**
     * Returns the number of Consumers.
     *
     * @return int The number of Consumers
     */
    public function count()
    {
        return count($this->consumers);
    }
}