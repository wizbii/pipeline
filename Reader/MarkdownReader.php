<?php

namespace Wizbii\PipelineBundle\Reader;

use Wizbii\CommonBundle\Utils\StringUtils;
use Wizbii\PipelineBundle\Model\Pipeline;
use Wizbii\PipelineBundle\Model\Task;

class MarkdownReader implements Reader
{
    /**
     * @param resource $stream
     * @param array $options
     * @return Pipeline[]
     */
    public function read($stream, $options = [])
    {
        while (($line = stream_get_line($stream, 4096)) !== false) {
            $this->readLine($line);
        }

        return $this->pipelines;
    }

    protected function readLine($line)
    {
        // ignore empty lines or lines starting with a comment (#)
        if (trim($line) === "") return;
        if ($this->stringUtils->startsWith("#", trim($line))) return;
        if ($this->stringUtils->startsWith("//", trim($line))) return;

        // if line does not start with a space, it means it's a new pipeline
        if (!$this->stringUtils->startsWith(" ", $line)) {
            $this->openNewPipeline(trim($line));
        }
        else {
            // otherwise, it's a task in the current pipeline
            $pattern = '(?<leadingSpaces>\s+)(\- )(?<name>\w*)';
            if (!preg_match($pattern, $line, $matches)) {
                throw new \ParseError("Can't get taskName from '$line' using '$pattern'");
            }
            $indentation = count($matches["leadingSpaces"]);
            if ($indentation === 0) {
                throw new \ParseError("Tasks should be indented. Error on '$line'");
            }

            // search for the parent task based on indentation
            $parentTask = null;
            while (!isset($parentTask)) {
                $t = array_pop($this->tasksStack);
                if ($t["indentation"] < $indentation) $parentTask = $t["task"];
            }

            $currentTask = new Task($matches["name"]);
            $parentTask->addTask($currentTask);
            $this->stackTask($currentTask, $indentation);
        }
    }

    protected function openNewPipeline($name)
    {
        if (isset($this->currentPipeline)) $this->closeCurrentPipeline();
        $this->currentPipeline = new Pipeline($name);
        $this->tasksStack = [];
        $this->stackTask($this->currentPipeline, 0);
    }

    protected function stackTask($task, $indentation)
    {
        $this->tasksStack[] = [
            "task"        => $task,
            "indentation" => $indentation
        ];
    }

    protected function closeCurrentPipeline()
    {
        $this->pipelines[] = $this->currentPipeline;
    }

    public function __construct()
    {
        $this->stringUtils = new StringUtils();
    }

    /**
     * @var StringUtils
     */
    protected $stringUtils;

    /**
     * @var Pipeline
     */
    protected $currentPipeline;

    /**
     * @var Pipeline[]
     */
    protected $pipelines;

    /**
     * @var Task[]
     */
    protected $tasksStack;
}