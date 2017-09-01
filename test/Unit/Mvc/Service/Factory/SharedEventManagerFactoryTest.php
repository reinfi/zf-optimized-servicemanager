<?php

namespace Reinfi\OptimizedServiceManager\Unit\Mvc\Service\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Mvc\Service\Factory\SharedEventManagerFactory;
use Zend\EventManager\SharedEventManager;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Mvc\Service\Factory
 */
class SharedEventManagerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesServiceListener()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $factory = new SharedEventManagerFactory();

        $instance = $factory($container->reveal());

        $this->assertInstanceOf(
            SharedEventManager::class, $instance,
            'instance should be of class ' . SharedEventManager::class
        );
    }
}