<?php

namespace Wizbii\PipelineBundle\Model;

class Task
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Task[]
     */
    protected $followingTasks;

    /**
     * Task constructor.
     * @param string $name
     * @param Task[] $followingTasks
     */
    public function __construct($name, $followingTasks = [])
    {
        $this->name = $name;
        $this->followingTasks = $followingTasks;
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
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Task[]
     */
    public function getFollowingTasks()
    {
        return $this->followingTasks;
    }

    /**
     * @param Task[] $followingTasks
     */
    public function setFollowingTasks($followingTasks)
    {
        $this->followingTasks = $followingTasks;
    }

    /**
     * @param Task $task
     */
    public function addFollowingTask($task)
    {
        $this->followingTasks[] = $task;
    }
}