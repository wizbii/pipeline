<?php

namespace Wizbii\PipelineBundle\Model;

class Action
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * Action constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addProperty($key, $value): void
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasProperty($key)
    {
        return is_array($this->properties) && array_key_exists($key, $this->properties);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function &getProperty($key, $default = null)
    {
        $value = $default;
        if ($this->hasProperty($key)) {
            $value = $this->properties[$key];
        }

        return $value;
    }

    /**
     * @param string $key
     */
    public function removeProperty($key): void
    {
        if ($this->hasProperty($key)) {
            unset($this->properties[$key]);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "[Action] name : '".$this->name."' ; content : ".json_encode($this->properties);
    }
}
