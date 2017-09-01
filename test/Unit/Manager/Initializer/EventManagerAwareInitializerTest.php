<?php

namespace Reinfi\OptimizedServiceManager\Unit\Manager\Initializer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Manager\Initializer\EventManagerAwareInitializer;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Manager\Initializer
 */
class EventManagerAwareInitializerTest extends TestCase
{
    /**
     * @test
     */
    public function itDoesNothingIfNotAwareInterface()
    {
        $initializer = new EventManagerAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);

        $instance = $this->prophesize(EventManagerInterface::class);

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itSetsEventManagerIfNoneIsSet()
    {
        $initializer = new EventManagerAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->get(Argument::exact('EventManager'))
            ->willReturn($this->prophesize(EventManager::class)->reveal());

        $instance = $this->prophesize(EventManagerAwareInterface::class);

        $instance->getEventManager()
            ->willReturn(null);

        $instance->setEventManager(Argument::type(EventManager::class))
            ->shouldBeCalled();

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itDoesNothingIfEventManagerIsSet()
    {
        $initializer = new EventManagerAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->get(Argument::exact('EventManager'))
            ->willReturn($this->prophesize(EventManager::class)->reveal());

        $instance = $this->prophesize(EventManagerAwareInterface::class);

        $eventManager = $this->prophesize(EventManager::class);
        $eventManager->getSharedManager()
            ->willReturn($this->prophesize(SharedEventManagerInterface::class)->reveal());

        $instance->getEventManager()
            ->willReturn($eventManager->reveal());

        $initializer->initialize($instance->reveal(), $container->reveal());
    }

    /**
     * @test
     */
    public function itSetsEventManagerIfSharedEventManagerIsNotSet()
    {
        $initializer = new EventManagerAwareInitializer();

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->get(Argument::exact('EventManager'))
            ->willReturn($this->prophesize(EventManager::class)->reveal());

        $instance = $this->prophesize(EventManagerAwareInterface::class);

        $eventManager = $this->prophesize(EventManager::class);
        $eventManager->getSharedManager()
            ->willReturn(null);

        $instance->getEventManager()
            ->willReturn($eventManager->reveal());

        $container->get(Argument::exact('EventManager'))
            ->willReturn($this->prophesize(EventManager::class)->reveal());

        $instance = $this->prophesize(EventManagerAwareInterface::class);

        $instance->getEventManager()
            ->willReturn(null);

        $instance->setEventManager(Argument::type(EventManager::class))
            ->shouldBeCalled();

        $initializer->initialize($instance->reveal(), $container->reveal());
    }
}