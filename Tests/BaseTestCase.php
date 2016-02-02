<?php

namespace Wizbii\PipelineBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseTestCase extends WebTestCase
{

    protected function setup()
    {
        self::$kernel = self::createKernel();
        self::$kernel->boot();
    }

    protected function getService($serviceId)
    {
        return static::$kernel->getContainer()->get($serviceId, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    protected function getParameter($parameterId)
    {
        return static::$kernel->getContainer()->getParameter($parameterId);
    }
}