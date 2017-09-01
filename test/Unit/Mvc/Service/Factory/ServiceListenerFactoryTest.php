<?php

namespace Reinfi\OptimizedServiceManager\Unit\Mvc\Service\Factory;

use PHPUnit\Framework\TestCase;
use Reinfi\OptimizedServiceManager\Mvc\Service\Factory\ServiceListenerFactory;
use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Mvc\Service\Factory
 */
class ServiceListenerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesServiceListener()
    {
        $container = $this->prophesize(ServiceManager::class);

        $factory = new ServiceListenerFactory();

        $instance = $factory($container->reveal());

        $this->assertInstanceOf(
            ServiceListener::class, $instance,
            'instance should be of class ' . ServiceListener::class
        );
    }
}