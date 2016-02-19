<?php

namespace Wizbii\PipelineBundle\Matcher;

use Wizbii\PipelineBundle\Matcher\Base\AndMatcher;
use Wizbii\PipelineBundle\Matcher\Base\Matcher;
use Wizbii\PipelineBundle\Model\Action;

class ActionMatcher
{
    /**
     * @var Action
     */
    protected $action;

    /**
     * @var Matcher[]
     */
    protected $matchersOnActionName = [];

    /**
     * @var Matcher[]
     */
    protected $matchersOnPropertyName = [];

    /**
     * @var callable[]
     */
    protected $executors = [];

    /**
     * @var AndMatcher
     */
    protected $currentMatcher;

    /**
     * @return AndMatcher
     */
    public function addMatcherOnActionName()
    {
        $this->currentMatcher = AndMatcher::build();
        $this->matchersOnActionName[] = $this->currentMatcher;

        return $this->currentMatcher;
    }

    /**
     * @param string $propertyName
     * @return AndMatcher
     */
    public function addMatcherOnPropertyName($propertyName)
    {
        $this->currentMatcher = AndMatcher::build();
        $this->matchersOnPropertyName[$propertyName] = $this->currentMatcher;

        return $this->currentMatcher;
    }

    /**
     * @param callable $callable
     */
    public function addExecutor($callable)
    {
        $this->executors[] = $callable;
    }

    /**
     * @param Action $action
     * @return bool
     */
    public function matches($action)
    {
        // validates on action name
        foreach ($this->matchersOnActionName as $matcher) {
            if (!$matcher->matches($action->getName())) {
                return false;
            }
        }

        // validates on properties
        foreach ($this->matchersOnPropertyName as $propertyName => $matcher) {
            if (!$matcher->matches($action->getProperty($propertyName))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return callable[]
     */
    public function getExecutors()
    {
        return $this->executors;
    }

    /**
     * @return AndMatcher
     */
    public function getCurrentMatcher()
    {
        return $this->currentMatcher;
    }
}