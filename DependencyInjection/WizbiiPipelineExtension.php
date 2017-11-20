<?php

namespace Wizbii\PipelineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Wizbii\PipelineBundle\Consumer\CommandConsumer;
use Wizbii\PipelineBundle\Factory\PipelineFactory;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WizbiiPipelineExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
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
        $container->setDefinition("pipeline.provider", $pipelineProviderDefinition);

        // Load Connection
        $this->loadConnection();

        // create global producer
        $internalProducerDefinition = new Definition("%pipeline.producer.class%");
        $pipelineExchangeName = $pipeline->getName();
        $internalProducerDefinition->addTag('old_sound_rabbit_mq.base_amqp')
                                   ->addTag('old_sound_rabbit_mq.producer')
                                   ->addMethodCall('setExchangeOptions', [[
                                       "name" => $pipelineExchangeName,
                                       "type" => "direct",
                                       "passive" => false,
                                       "durable" => true]])
                                   ->addMethodCall('setQueueOptions', [["name" => null]])
                                   ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'))
                                   ->setProperty("logger", new Reference("monolog.logger.pipeline"));
        $internalProducerId = 'old_sound_rabbit_mq.internal_pipeline_producer';
        $this->container->setDefinition($internalProducerId, $internalProducerDefinition);

        // create event consumers for each incoming event
        foreach ($pipeline->getIncomingEvents() as $event) {
            $frontConsumerDefinition = new Definition("%pipeline.consumer.front.class%");
            $frontConsumerDefinition->setProperty("eventName", $event->getName())
                                    ->setProperty("producer", new Reference($internalProducerId))
                                    ->setPublic(false);
            $frontConsumerId = sprintf('pipeline.consumer.front.%s_consumer', $event->getName());
            $this->container->setDefinition($frontConsumerId, $frontConsumerDefinition);

            $amqpConsumer = new Definition('%pipeline.consumer.command.class%');
            $amqpConsumer
                ->addTag('old_sound_rabbit_mq.base_amqp')
                ->addTag('old_sound_rabbit_mq.consumer')
                ->addTag('pipeline.front.consumer')
                ->addMethodCall('setExchangeOptions', [["name" => $event->getName(), "type" => "direct"]])
                ->addMethodCall('setQueueOptions', [["name" => $event->getName()]])
                ->addMethodCall('setCallback', [[new Reference($frontConsumerId), "execute"]])
                ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'));
            $name = sprintf('old_sound_rabbit_mq.%s_consumer', $event->getName());

            $this->setConsumerProcessTitle($amqpConsumer, "front_consumer_".$event->getName());

            $this->container->setDefinition($name, $amqpConsumer);
        }

        // create event producers for each outgoing event
        foreach ($pipeline->getOutgoingEvents() as $event) {
            $producerDefinition = new Definition("%pipeline.producer.class%");
            $producerDefinition->setProperty("logger", new Reference("monolog.logger.pipeline"))
                               ->addTag('old_sound_rabbit_mq.producer')
                               ->addTag('pipeline.back.producer')
                               ->addMethodCall('setExchangeOptions', [[
                                   "name" => $event->getName(),
                                   "type" => "direct",
                                   "passive" => false,
                                   "durable" => true]])
                               ->addMethodCall('setQueueOptions', [["name" => null]])
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
            ->addMethodCall('setExchangeOptions', [["name" => $pipelineExchangeName, "type" => "direct"]])
            ->addMethodCall('setQueueOptions', [["name" => $pipelineQueueName]])
            ->addMethodCall('setCallback', [[new Reference("pipeline.consumer.back"), "execute"]])
            ->addArgument(new Reference('old_sound_rabbit_mq.connection.default'));

        $this->setConsumerProcessTitle($backConsumer, $pipelineQueueName);

        $this->container->setDefinition("old_sound_rabbit_mq.pipeline_back_consumer", $backConsumer);
    }

    protected function loadConnection()
    {
        $classParam = '%old_sound_rabbit_mq.lazy.connection.class%';
        $definition = new Definition('%old_sound_rabbit_mq.connection_factory.class%', array($classParam, $this->config["connection"]));
        $definition->setPublic(false);
        $factoryName = sprintf('old_sound_rabbit_mq.connection_factory.%s', "default");
        $this->container->setDefinition($factoryName, $definition);

        $definition = new Definition($classParam);
        $definition->setFactory(array(new Reference($factoryName), 'createConnection'));

        $this->container->setDefinition(sprintf('old_sound_rabbit_mq.connection.%s', "default"), $definition);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig("monolog", [
            "channel" => [
                "pipeline", "pipeline_action_dispatcher"
            ],
            "handlers" => [
                "pipeline_action_dispatcher" => [
                    "type"      => "stream",
                    "level"     => "info",
                    "path"      => $container->getParameter("kernel.logs_dir") . '/pipeline_action_dispatcher.log',
                    "channels"  => "pipeline_action_dispatcher",
                    "formatter" => "wizbii.monolog.formatter.raw"
                ],
                "pipeline" => [
                    "type"      => "stream",
                    "level"     => "error",
                    "path"      => $container->getParameter("kernel.logs_dir") . '/pipeline.log',
                    "channels"  => "pipeline",
                    "formatter" => "wizbii.monolog.formatter.raw"
                ]
            ]
        ]);

    }

    private function setConsumerProcessTitle(Definition $definition, string $procTitle)
    {
        if ($definition->getClass() === CommandConsumer::class) {
            $definition->addMethodCall('setProcessTitle', [$procTitle]);
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
