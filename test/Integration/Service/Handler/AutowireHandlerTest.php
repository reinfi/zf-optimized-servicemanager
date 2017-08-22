<?php

namespace Reinfi\OptimizedServiceManager\Integration\Service\Handler;

use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Injection\AutoWiringPluginManager;
use Reinfi\DependencyInjection\Injection\InjectionInterface;
use Reinfi\DependencyInjection\Service\AutoWiring\ResolverService;
use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler;
use Reinfi\OptimizedServiceManager\Service\Service1;
use Reinfi\OptimizedServiceManager\Service\Service2;
use Reinfi\OptimizedServiceManager\Service\Service3;
use Reinfi\OptimizedServiceManager\Service\Service4;
use Reinfi\OptimizedServiceManager\Service\Service5;
use Reinfi\OptimizedServiceManager\Types\AutoWire;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Service\Handler
 */
class AutowireHandlerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itResolvesAutoWireInjection()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        /** @var AutowireHandler $handler */
        $handler = $serviceManager->get(AutowireHandler::class);

        $type = new AutoWire(Service1::class);

        $method = $handler->handle($type);

        $this->assertEquals(
            Service1::class,
            $method->getClassName()
        );

        $this->assertCount(
            4,
            $method->getMethodBody(),
            'There should be four method body parts'
        );

        $this->assertContains(
            sprintf(
                '$this->get(\'%s\'),', Service2::class
            ),
            $method->getMethodBody()
        );

        $this->assertContains(
            sprintf(
                '$this->get(\'%s\')', Service3::class
            ),
            $method->getMethodBody()
        );
    }

    /**
     * @test
     */
    public function itResolvesAutowireOfAbstractFactoryService()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        /** @var AutowireHandler $handler */
        $handler = $serviceManager->get(AutowireHandler::class);

        $type = new AutoWire(Service5::class);

        $method = $handler->handle($type);

        $this->assertEquals(
            Service5::class,
            $method->getClassName()
        );

        $this->assertCount(
            3,
            $method->getMethodBody(),
            'There should be three method body parts'
        );

        $this->assertContains(
            sprintf(
                '$this->container->get(\'%s\')',
                Service4::class
            ),
            $method->getMethodBody()
        );
    }

    /**
     * @test
     */
    public function itResolvesAutoWirePluginManagerInjection()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        $resolverService = $this->prophesize(ResolverService::class);
        $resolverService->resolve(Argument::exact(Service1::class))
            ->willReturn([
                 new AutoWiringPluginManager('PluginManager', Service2::class)
             ]);
        $serviceManager->setAllowOverride(true)
            ->setService(ResolverService::class, $resolverService->reveal());

        /** @var AutowireHandler $handler */
        $handler = $serviceManager->get(AutowireHandler::class);

        $type = new AutoWire(Service1::class);

        $method = $handler->handle($type);

        $this->assertEquals(
            Service1::class,
            $method->getClassName()
        );

        $this->assertCount(
            3,
            $method->getMethodBody(),
            'There should be three method body parts'
        );

        $this->assertContains(
            sprintf(
                '$this->container->get(\'%s\')->get(\'%s\')',
                'PluginManager',
                Service2::class
            ),
            $method->getMethodBody()
        );
    }

    /**
     * @test
     */
    public function itResolvesOtherInjectionTypes()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        $inject = $this->prophesize(InjectionInterface::class);
        $invokeMethod = new MethodProphecy(
            $inject, '__invoke', [ Argument::exact($serviceManager) ]
        );
        $invokeMethod->willReturn(new Service2());
        $inject->addMethodProphecy($invokeMethod);

        $resolverService = $this->prophesize(ResolverService::class);
        $resolverService->resolve(Argument::exact(Service1::class))
            ->willReturn([
                 $inject->reveal()
             ]);
        $serviceManager->setAllowOverride(true)
            ->setService(ResolverService::class, $resolverService->reveal());

        /** @var AutowireHandler $handler */
        $handler = $serviceManager->get(AutowireHandler::class);

        $type = new AutoWire(Service1::class);

        $method = $handler->handle($type);

        $this->assertEquals(
            Service1::class,
            $method->getClassName()
        );

        $this->assertCount(
            3,
            $method->getMethodBody(),
            'There should be three method body parts'
        );

        $this->assertContains(
            sprintf(
                '$this->container->get(\'%s\')',
                Service2::class
            ),
            $method->getMethodBody()
        );
    }
}