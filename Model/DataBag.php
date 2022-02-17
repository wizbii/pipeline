<?php

namespace Wizbii\PipelineBundle\Model;

/**
 * @phpstan-implements \IteratorAggregate<string, mixed>
 */
class DataBag implements \IteratorAggregate, \Countable
{
    const OPTION_PRIORITY = '_priority';

    /**
     * Parameter storage.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters An array of parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @return array An array of parameters
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array $parameters An array of parameters
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters.
     *
     * @param array $parameters An array of parameters
     */
    public function add(array $parameters = []): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $key     The key
     * @param string $default The default value
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    /**
     * Sets a parameter by name.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     */
    public function set($key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Removes a parameter.
     *
     * @param string $key The key
     */
    public function remove($key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * @param int $priority A priority that will be added to the RabbitMQ message. Should be between 0 and 10.
     * @throws InvalidArgumentException
     */
    public function setPriority(int $priority): void
    {
        if ($priority < 0 || $priority > 10) {
            throw new \InvalidArgumentException('Priority must be an integer between 0-10');
        }

        $this->set(self::OPTION_PRIORITY, $priority);
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return \ArrayIterator<string, mixed> An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count()
    {
        return count($this->parameters);
    }
}
