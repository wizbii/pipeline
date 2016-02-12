<?php

namespace Wizbii\PipelineBundle\Runnable;

use Wizbii\PipelineBundle\Exception\StoreNotRunnableException;
use Wizbii\PipelineBundle\Matcher\ActionMatcher;
use Wizbii\PipelineBundle\Matcher\Base\AndMatcher;
use Wizbii\PipelineBundle\Matcher\Base\CallableMatcher;
use Wizbii\PipelineBundle\Matcher\Base\ContainsKeys;
use Wizbii\PipelineBundle\Matcher\Base\EmptyMatcher;
use Wizbii\PipelineBundle\Matcher\Base\GreaterThanOrEquals;
use Wizbii\PipelineBundle\Matcher\Base\In;
use Wizbii\PipelineBundle\Matcher\Base\Is;
use Wizbii\PipelineBundle\Matcher\Base\IsArray;
use Wizbii\PipelineBundle\Matcher\Base\LessThanOrEquals;
use Wizbii\PipelineBundle\Matcher\Base\Matcher;
use Wizbii\PipelineBundle\Matcher\Base\Not;
use Wizbii\PipelineBundle\Model\Action;
use Wizbii\PipelineBundle\Model\DataBag;

abstract class DispatcherStore extends BaseStore
{
    /**
     * @var ActionMatcher[]
     */
    protected $actionMatchers = [];

    /**
     * @var callable[]
     */
    protected $beforeDispatchExecutors = [];

    /**
     * @var callable[]
     */
    protected $afterDispatchExecutors = [];

    /**
     * DispatcherStore constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->configure();
    }

    /**
     * Configure the current store regarding input action and executors
     */

    /**
     * @inheritdoc
     */
    public function run($action)
    {
        $eventsConfig = [];
        $hasMatched = false;
        foreach ($this->beforeDispatchExecutors as $executor) {
            $this->runExecutor($executor, $action, $eventsConfig);
        }
        foreach ($this->actionMatchers as $actionMatcher) {
            if ($actionMatcher->matches($action)) {
                $hasMatched = true;
                foreach ($actionMatcher->getExecutors() as $executor) {
                    $this->runExecutor($executor, $action, $eventsConfig);
                }
            }
        }
        foreach ($this->afterDispatchExecutors as $executor) {
            $this->runExecutor($executor, $action, $eventsConfig);
        }

        if (!$hasMatched) {
            $eventsConfig = $this->onDispatchFailure($action);
        }

        return $eventsConfig;
    }

    /**
     * @param callable $executor
     * @param Action $action
     * @param DataBag[] $eventsConfig
     */
    protected function runExecutor($executor, $action, &$eventsConfig)
    {
        $e = call_user_func_array($executor, [$action]);
        // aggregate eventsConfig
        if (isset($e)) {
            $e = is_array($e) ? $e : [$e];
            $eventsConfig = array_merge($eventsConfig, $e);
        }
    }

    /**
     * Configure the dispatcher
     */
    protected abstract function configure();

    /**
     * Let store decide what to do when the dispatch process failed
     * @param Action $action
     * @return DataBag[]
     * @throws StoreNotRunnableException
     */
    public function onDispatchFailure($action)
    {
        throw new StoreNotRunnableException("Cant' dispatch action " . $action->__toString() . " on store " . $this->getName() . " : It does not match anything");
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function executeBeforeDispatch($callable)
    {
        $this->beforeDispatchExecutors[] = $this->buildExecutor($callable);

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function executeAfterDispatch($callable)
    {
        $this->afterDispatchExecutors[] = $this->buildExecutor($callable);

        return $this;
    }

    /**
     * @return $this
     */
    public function newActionMatcher()
    {
        $this->actionMatchers[] = new ActionMatcher();

        return $this;
    }

    /**
     * @return $this
     */
    public function ifActionName()
    {
        $this->getLastActionMatcher()->addMatcherOnActionName();

        return $this;
    }

    /**
     * @param string $propertyName
     * @return $this
     */
    public function ifProperty($propertyName)
    {
        $this->getLastActionMatcher()->addMatcherOnPropertyName($propertyName);

        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function is($value)
    {
        return $this->addMatcher(Is::build($value));
    }

    /**
     * @return $this
     */
    public function isNotEmpty()
    {
        return $this->addMatcher(Not::build(EmptyMatcher::build()));
    }

    /**
     * @return $this
     */
    public function isEmpty()
    {
        return $this->addMatcher(EmptyMatcher::build());
    }

    /**
     * @param mixed $minValue
     * @param mixed $maxValue
     * @return $this
     */
    public function isBetween($minValue, $maxValue)
    {
        return $this->addMatcher(GreaterThanOrEquals::build($minValue))
                    ->addMatcher(LessThanOrEquals::build($maxValue));
    }

    /**
     * @param mixed $acceptedValues
     * @return $this
     */
    public function in($acceptedValues)
    {
        return $this->addMatcher(In::build($acceptedValues));
    }

    /**
     * @param mixed $acceptedValues
     * @return $this
     */
    public function containsKeys($acceptedValues)
    {
        return $this->addMatcher(ContainsKeys::build($acceptedValues));
    }

    /**
     * @return $this
     */
    public function isArray()
    {
        return $this->addMatcher(IsArray::build());
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function matches($callable)
    {
        return $this->addMatcher(CallableMatcher::build($callable));
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function thenExecute($callable)
    {
        $this->getLastActionMatcher()->addExecutor($this->buildExecutor($callable));

        return $this;
    }

    /**
     * @param Matcher $matcher
     * @return $this
     */
    protected function addMatcher($matcher)
    {
        $this->getLastActionMatcher()->getCurrentMatcher()->addMatcher($matcher);
        return $this;
    }

    /**
     * @return ActionMatcher
     */
    protected function getLastActionMatcher()
    {
        return $this->actionMatchers[count($this->actionMatchers) - 1];
    }

    /**
     * @param callable $callable
     * @return callable
     */
    protected function buildExecutor($callable)
    {
        if (is_string($callable)) {
            // special case for method inside current class
            $callable = [$this, $callable];
        }

        return $callable;
    }
}