<?php

namespace Wizbii\PipelineBundle\Service;

use Countable;
use IteratorAggregate;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * @phpstan-implements IteratorAggregate<string, Producer>
 */
class Producers implements IteratorAggregate, Countable
{
    /**
     * Parameter storage.
     *
     * @var Producer[]
     */
    protected $producers;

    /**
     * Constructor.
     *
     * @param Producer[] $producers An array of Producer
     */
    public function __construct(array $producers = [])
    {
        $this->producers = $producers;
    }

    /**
     * Returns the $producers.
     *
     * @return Producer[] An array of producers
     */
    public function all()
    {
        return $this->producers;
    }

    /**
     * Returns the parameter keys.
     *
     * @return string[] An array of Producer keys
     */
    public function keys(): array
    {
        return array_keys($this->producers);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param Producer[] $producers An array of producers
     */
    public function replace(array $producers = []): void
    {
        $this->producers = $producers;
    }

    /**
     * Adds producers.
     *
     * @param Producer[] $producers An array of producers
     */
    public function add(array $producers = []): void
    {
        $this->producers = array_replace($this->producers, $producers);
    }

    /**
     * Returns a Producer by name.
     *
     * @param string   $key     The key
     * @param Producer $default The default value
     *
     * @return Producer|null
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->producers) ? $this->producers[$key] : $default;
    }

    /**
     * Sets a Producer by name.
     *
     * @param string   $key      The key
     * @param Producer $producer The value
     */
    public function set($key, $producer): void
    {
        $this->producers[$key] = $producer;
    }

    /**
     * Returns true if the Producer is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the Producer exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->producers);
    }

    /**
     * Removes a Producer.
     *
     * @param string $key The key
     */
    public function remove($key): void
    {
        unset($this->producers[$key]);
    }

    /**
     * Returns an iterator for Producers.
     *
     * @return \ArrayIterator<string, Producer>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->producers);
    }

    /**
     * Returns the number of Producers.
     *
     * @return int The number of Producers
     */
    public function count()
    {
        return count($this->producers);
    }
}
