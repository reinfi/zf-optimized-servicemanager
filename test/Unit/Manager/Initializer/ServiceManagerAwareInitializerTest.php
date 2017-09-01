<?php

namespace Reinfi\OptimizedServiceManager\Unit\Manager\Initializer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Reinfi\OptimizedServiceManager\Manager\Initializer\ServiceManagerAwareInitializer;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Manager\Initializer
 */
class ServiceManagerAwareInitializerTest extends TestCase
{
    /**
     * @test
     */
    public function itDoesNothingIfNotAwareInterface()
    {
        $initializer = new ServiceManagerAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);

        $instance = $this->prophesize(ServiceLocatorInterface::class);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itSetsServiceManagerIfAwareInterface()
    {
        $initializer = new ServiceManagerAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(ServiceManagerAwareInterface::class);

        $instance->setServiceManager(
            Argument::type(ServiceLocatorInterface::class)
        )
            ->shouldBeCalled();

        @$initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itTriggersErrorAsDeprecation()
    {
        $this->expectException(\PHPUnit_Framework_Error_Deprecated::class);

        $initializer = new ServiceManagerAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(ServiceManagerAwareInterface::class);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }
}