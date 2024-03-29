<?php

namespace Wizbii\PipelineBundle\DependencyInjection;

use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Wizbii\PipelineBundle\Consumer\CommandConsumer;
use Wizbii\PipelineBundle\Consumer\DirectConsumer;
use Wizbii\PipelineBundle\Consumer\MultipleConsumer;
use Wizbii\PipelineBundle\Factory\PipelineFactory;
use Wizbii\PipelineBundle\Model\Event;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WizbiiPipelineExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration = new Configuration();
        $this->config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // create pipeline definition and store it into a PipelineProvider
        $pipeline = (new PipelineFactory())->buildPipeline($this->config);
        $pipelineProviderDefinition = new Definition('Wizbii\PipelineBundle\Service\PipelineProvider', [$this->config]);
        $container->setDefinition('pipeline.provider', $pipelineProviderDefinition);

        // Load Connection
        $this->loadConnection();

        // create global producer
        $internalProducerDefinition = new Definition('%pipeline.producer.class%');
        $pipelineExchangeName = $pipeline->getName();
        $internalProducerDefinition->addTag('old_sound_rabbit_mq.base_amqp')
                                   ->addTag('old_sound_rabbit_mq.producer')
                                   ->addMethodCall('setExchangeOptions', [[
                                       'name' => $pipelineExchangeName,
                                       'type' => 'direct',
                                       'passive' => false,
                                       'durable' => true, ]])
                                   ->addMethodCall('setQueueOptions', [['name' => null, 'declare' => false]])
                                   ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'))
                                   ->setProperty('logger', new Reference('monolog.logger.pipeline'));
        $internalProducerId = 'old_sound_rabbit_mq.internal_pipeline_producer';
        $this->container->setDefinition($internalProducerId, $internalProducerDefinition);

        $frontMultiConsumer = $this->container->register('pipeline.consumer.front_multi', MultipleConsumer::class)
            ->setPublic(true)
            ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'))
            ->addMethodCall('setQosOptions', [0, 200]);

        $this->addConsumerTimeouts($frontMultiConsumer);

        // create event consumers for each incoming event
        foreach ($pipeline->getIncomingEvents() as $event) {
            $this->configureFrontConsumer($event, $internalProducerId);

            if ($this->config['actions'][$event->getName()]['type'] !== 'direct') {
                $frontMultiConsumer->addMethodCall('addQueue', [[
                    'name' => $event->getName(),
                    'callback' => [
                        new Reference(sprintf('pipeline.consumer.front.%s_consumer', $event->getName())),
                        'execute',
                    ],
                ]]);
            }
        }

        // create event producers for each outgoing event
        foreach ($pipeline->getOutgoingEvents() as $event) {
            $producerDefinition = new Definition('%pipeline.producer.class%');
            $producerDefinition->setProperty('logger', new Reference('monolog.logger.pipeline'))
                               ->addTag('old_sound_rabbit_mq.producer')
                               ->addTag('pipeline.back.producer')
                               ->addMethodCall('setExchangeOptions', [[
                                   'name' => $event->getName(),
                                   'type' => 'direct',
                                   'passive' => false,
                                   'durable' => true, ]])
                               ->addMethodCall('setQueueOptions', [['name' => null, 'declare' => false]])
                               ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'));
            $producerId = sprintf('pipeline.producer.%s', $event->getName());
            $this->container->setDefinition($producerId, $producerDefinition);
        }

        // create backend consumer
        $pipelineQueueName = $pipeline->getName();
        $backConsumer = new Definition('%old_sound_rabbit_mq.consumer.class%');
        $backConsumer
            ->addTag('old_sound_rabbit_mq.base_amqp')
            ->addTag('old_sound_rabbit_mq.consumer')
            ->addMethodCall('setExchangeOptions', [['name' => $pipelineExchangeName, 'type' => 'direct']])
            ->addMethodCall('setQueueOptions', [['name' => $pipelineQueueName, 'declare' => false]])
            ->addMethodCall('setQosOptions', [0, 200])
            ->addMethodCall('setCallback', [[new Reference('pipeline.consumer.back'), 'execute']])
            ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'));

        $this->addConsumerTimeouts($backConsumer);
        $this->setConsumerProcessTitle($backConsumer, $pipelineQueueName);

        $this->container->setDefinition('old_sound_rabbit_mq.pipeline_back_consumer', $backConsumer);
    }

    protected function loadConnection()
    {
        $classParam = '%old_sound_rabbit_mq.lazy.connection.class%';
        $definition = new Definition('%old_sound_rabbit_mq.connection_factory.class%', [$classParam, $this->config['connection']]);
        $definition->setPublic(false);
        $factoryName = sprintf('old_sound_rabbit_mq.connection_factory.%s', 'default');
        $this->container->setDefinition($factoryName, $definition);

        $definition = new Definition($classParam);
        $definition->setFactory([new Reference($factoryName), 'createConnection']);

        $this->container->setDefinition(sprintf('old_sound_rabbit_mq.connection.%s', 'default'), $definition);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('monolog', [
            'channel' => [
                'pipeline', 'pipeline_action_dispatcher',
            ],
            'handlers' => [
                'pipeline_action_dispatcher' => [
                    'type' => 'stream',
                    'level' => 'info',
                    'path' => $container->getParameter('kernel.logs_dir').'/pipeline_action_dispatcher.log',
                    'channels' => 'pipeline_action_dispatcher',
                    'formatter' => 'wizbii.monolog.formatter.raw',
                ],
                'pipeline' => [
                    'type' => 'stream',
                    'level' => 'error',
                    'path' => $container->getParameter('kernel.logs_dir').'/pipeline.log',
                    'channels' => 'pipeline',
                    'formatter' => 'wizbii.monolog.formatter.raw',
                ],
            ],
        ]);
    }

    private function setConsumerProcessTitle(Definition $definition, string $procTitle)
    {
        $childDef = new ChildDefinition($definition);
        $childDef->addMethodCall('setProcessTitle', [$procTitle]);

        $definition->setInstanceofConditionals([
            CommandConsumer::class => $childDef,
        ]);
    }

    private function configureFrontConsumer(Event $event, string $internalProducerId): void
    {
        $eventName = $event->getName();
        $withPriority = $this->config['actions'][$eventName]['with_priority'];

        $queueArguments = $withPriority ? (new Definition(AMQPTable::class, [['x-max-priority' => 10]])) : null;

        if ($this->config['actions'][$eventName]['type'] === 'direct') {
            $frontConsumerDefinition = new Definition(DirectConsumer::class);
            $frontConsumerDefinition
                ->addArgument($eventName)
                ->addArgument(new Reference('pipeline.provider'))
                ->addArgument(new Reference('pipeline.dispatcher.action'))
                ->addArgument(new Reference('monolog.logger.pipeline'))
                ->setPublic(false);

            $amqpConsumerTag = 'pipeline.front.consumer.direct';
            $procTitle = 'direct_front_consumer_'.$eventName;
        } else {
            $frontConsumerDefinition = new Definition('%pipeline.consumer.front.class%');
            $frontConsumerDefinition
                ->setProperty('eventName', $eventName)
                ->setProperty('producer', new Reference($internalProducerId))
                ->setProperty('logger', new Reference('monolog.logger.pipeline'))
                ->setPublic(false);

            $amqpConsumerTag = 'pipeline.front.consumer';
            $procTitle = 'front_consumer_'.$eventName;
        }

        $frontConsumerId = sprintf('pipeline.consumer.front.%s_consumer', $eventName);
        $this->container->setDefinition($frontConsumerId, $frontConsumerDefinition);

        $amqpConsumer = new Definition('%pipeline.consumer.command.class%');
        $amqpConsumer
            ->addTag('old_sound_rabbit_mq.base_amqp')
            ->addTag('old_sound_rabbit_mq.consumer')
            ->addTag($amqpConsumerTag)
            ->addMethodCall('setExchangeOptions', [['name' => $eventName, 'type' => 'direct']])
            ->addMethodCall('setQueueOptions', [['name' => $eventName, 'arguments' => $queueArguments]])
            ->addMethodCall('setQosOptions', [0, 200])
            ->addMethodCall('setCallback', [[new Reference($frontConsumerId), 'execute']])
            ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'));

        $name = sprintf('old_sound_rabbit_mq.%s_consumer', $eventName);

        $this->addConsumerTimeouts($amqpConsumer);
        $this->setConsumerProcessTitle($amqpConsumer, $procTitle);

        $this->container->setDefinition($name, $amqpConsumer);
    }

    private function addConsumerTimeouts(Definition $definition): void
    {
        if ($this->config['consumers']['idle_timeout'] > 0) {
            $definition->addMethodCall('setIdleTimeout', [$this->config['consumers']['idle_timeout']]);
            $definition->addMethodCall('setIdleTimeoutExitCode', [0]);
        }

        if ($this->config['consumers']['max_execution_time'] > 0) {
            $definition->addMethodCall('setGracefulMaxExecutionDateTimeFromSecondsInTheFuture', [$this->config['consumers']['max_execution_time']]);
            $definition->addMethodCall('setGracefulMaxExecutionTimeoutExitCode', [0]);
        }
    }

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var array
     */
    protected $config = [];
}
