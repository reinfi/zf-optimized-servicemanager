<?php

namespace Reinfi\OptimizedServiceManager\Unit\Controller\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Controller\Factory\GenerateControllerFactory;
use Reinfi\OptimizedServiceManager\Controller\GenerateController;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\Mvc\Controller\ControllerManager;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Controller\Factory
 */
class GenerateControllerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesController()
    {
        $controllerManager = $this->prophesize(ControllerManager::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(OptimizerServiceInterface::class)
            ->willReturn(
                $this->prophesize(OptimizerServiceInterface::class)->reveal()
            );
        $controllerManager->getServiceLocator()
            ->willReturn($container->reveal());

        $factory = new GenerateControllerFactory();

        $instance = $factory($controllerManager->reveal());

        $this->assertInstanceOf(
            GenerateController::class,
            $instance
        );
    }
}