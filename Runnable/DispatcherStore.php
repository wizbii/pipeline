<?php

namespace Wizbii\PipelineBundle\Runnable;

use Wizbii\PipelineBundle\Exception\StoreNotRunnableException;
use Wizbii\PipelineBundle\Matcher\ActionMatcher;
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
use Wizbii\PipelineBundle\Runnable\EventsGenerator\CollectionEventsGenerator;
use Wizbii\PipelineBundle\Runnable\EventsGenerator\ComposableEventsGenerator;
use Wizbii\PipelineBundle\Runnable\EventsGenerator\EventsGenerator;
use Wizbii\PipelineBundle\Runnable\EventsGenerator\NullEventsGenerator;

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
     * @var callable[]
     */
    protected $dispatchFailureExecutors = [];

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
        $composableEventsGenerator = new ComposableEventsGenerator();
        $hasMatched = false;

        foreach ($this->actionMatchers as $actionMatcher) {
            if ($actionMatcher->matches($action)) {
                if (!$hasMatched) {
                    foreach ($this->beforeDispatchExecutors as $executor) {
                        $composableEventsGenerator->addEventsGenerator($this->runExecutor($executor, $action));
                    }
                }

                foreach ($actionMatcher->getExecutors() as $executor) {
                    $composableEventsGenerator->addEventsGenerator($this->runExecutor($executor, $action));
                }

                if (!$hasMatched) {
                    foreach ($this->afterDispatchExecutors as $executor) {
                        $composableEventsGenerator->addEventsGenerator($this->runExecutor($executor, $action));
                    }
                }

                $hasMatched = true;
            }
        }

        if (!$hasMatched) {
            foreach ($this->dispatchFailureExecutors as $executor) {
                $composableEventsGenerator->addEventsGenerator($this->runExecutor($executor, $action));
            }
        }

        return $composableEventsGenerator;
    }

    /**
     * @param callable $executor
     * @param Action $action
     * @return EventsGenerator
     */
    protected function runExecutor($executor, $action)
    {
        $eventsGenerator = call_user_func_array($executor, [$action]);
        // aggregate eventsConfig
        if (isset($eventsGenerator)) {
            if (! $eventsGenerator instanceof EventsGenerator) {
                $eventsGenerator = new CollectionEventsGenerator(is_array($eventsGenerator) ? $eventsGenerator : [$eventsGenerator]);
            }
            return $eventsGenerator;
        }

        return new NullEventsGenerator();
    }

    /**
     * Configure the dispatcher
     */
    protected abstract function configure();

    /**
     * This method should only be called for tests purposes (helps handling callbacks towards mocked dependencies)
     */
    final public function reConfigure()
    {
        $this->actionMatchers = [];
        $this->beforeDispatchExecutors = [];
        $this->afterDispatchExecutors = [];
        $this->configure();
    }

    /**
     * Let store decide what to do when the dispatch process failed
     * @param Action $action
     * @return EventsGenerator
     * @throws StoreNotRunnableException
     */
    public function onDispatchFailure($action)
    {
        throw new StoreNotRunnableException("Cant' dispatch action " . $action->__toString() . " on store " . $this->getName() . " : It does not match anything");
    }

    /**
     * USed for debug purposes mainly
     * @param Action $action
     */
    public function dumpAction($action)
    {
        echo "Action '" . $action->getName() . "' : " . var_export($action->getProperties(), true) . "\n";
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
     * @param callable $callable
     * @return $this
     */
    public function executeOnDispatchFailure($callable)
    {
        $this->dispatchFailureExecutors[] = $this->buildExecutor($callable);

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
     * @param array $acceptedValues
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