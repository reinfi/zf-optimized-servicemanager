<?php

namespace Reinfi\OptimizedServiceManager\Unit\Manager\Initializer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Reinfi\OptimizedServiceManager\Manager\Initializer\ServiceLocatorAwareInitializer;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Manager\Initializer
 */
class ServiceLocatorAwareInitializerTest extends TestCase
{
    /**
     * @test
     */
    public function itDoesNothingIfNotAwareInterface()
    {
        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);

        $instance = $this->prophesize(ServiceLocatorInterface::class);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itSetsServiceManagerIfAwareInterface()
    {
        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(ServiceLocatorAwareInterface::class);

        $instance->setServiceLocator(
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

        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(ServiceLocatorAwareInterface::class);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itSetsServiceManagerIfAwareInterfaceAndAbstractPluginManager(
    )
    {
        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(AbstractPluginManager::class);

        $instance->getServiceLocator()
            ->willReturn(null);

        $instance->setServiceLocator(
            Argument::type(ServiceLocatorInterface::class)
        )
            ->shouldBeCalled();

        @$initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itDoesNothingIfServiceLocatorAlreadySet()
    {
        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(AbstractPluginManager::class);

        $instance->getServiceLocator()
            ->willReturn($container->reveal());

        $instance->setServiceLocator(
            Argument::type(ServiceLocatorInterface::class)
        )
            ->shouldNotBeCalled();

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itTriggersErrorAsDeprecationIfAbstractPluginManager()
    {
        $this->expectException(\PHPUnit_Framework_Error_Deprecated::class);

        $initializer = new ServiceLocatorAwareInitializer();

        $container = $this->prophesize(ServiceManager::class);

        $instance = $this->prophesize(AbstractPluginManager::class);

        $instance->getServiceLocator()
            ->willReturn(null);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }
}