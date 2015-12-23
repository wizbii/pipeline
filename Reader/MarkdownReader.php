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
        while (($line = stream_get_line($stream, 4096, "\n")) !== false) {
            $this->readLine($line);
        }
        $this->closeCurrentPipeline();

        return $this->pipelines;
    }

    protected function readLine($line)
    {
        $message = "read line '$line'\n";
        // ignore lines starting with a comment
        if ($this->stringUtils->startsWith(trim($line), "#")) return;
        if ($this->stringUtils->startsWith(trim($line), "//")) return;

        // empty lines mark the end of a pipeline
        if (trim($line) === "") {
            $this->closeCurrentPipeline();
            return;
        }

        // if line does not start with a space, it means it's a new pipeline
        if (!$this->stringUtils->startsWith($line, " ")) {
            $this->openNewPipeline(trim($line));
            return;
        }

        // otherwise, it's a task in the current pipeline
        $pattern = '/(?<leadingSpaces>\s+)(\- )(?<name>.*)$/';
        if (!preg_match($pattern, $line, $matches)) {
            throw new \ParseError("Can't get taskName from '$line' using '$pattern'");
        }
        $indentation = strlen($matches["leadingSpaces"]);
        if ($indentation === 0) {
            throw new \ParseError("Tasks should be indented. Error on '$line'");
        }
        $message .= " -> indentation : $indentation\n";

        // search for the parent task based on indentation
        $parentTask = null;
        while (!isset($parentTask)) {
            $t = array_pop($this->tasksStack);
            $message .= "   -> compare to " . $t["task"]->getName() . "/indentation => " . $t["indentation"] . "\n";
            if ($t["indentation"] < $indentation) {
                $parentTask = $t["task"];
                // re-stack the parent task as it is still valid
                $this->stackTask($parentTask, $t["indentation"]);
            }
        }

        $currentTask = new Task($matches["name"]);
        $parentTask->addFollowingTask($currentTask);
        $this->stackTask($currentTask, $indentation);
        //echo $message;
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
        if (!isset($this->currentPipeline)) return;
        $this->pipelines[] = $this->currentPipeline;
        $this->currentPipeline = null;
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