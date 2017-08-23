<?php

namespace Reinfi\OptimizedServiceManager\Unit\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Service\Handler\HandlerInterface;
use Reinfi\OptimizedServiceManager\Service\Handler\TestType;
use Reinfi\OptimizedServiceManager\Service\TypeHandlerService;

/**
 * Class TypeHandlerServiceTest
 *
 * @package Reinfi\OptimizedServiceManager\Unit\Service
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class TypeHandlerServiceTest extends TestCase
{
    /**
     * @test
     */
    public function itHandlesNewAddedType()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $method = new InstantiationMethod('TestClass');
        $method->addBodyPart('Test');

        $handler = $this->prophesize(HandlerInterface::class);
        $handler->handle(Argument::type(TestType::class))
            ->willReturn($method);

        $container->get('MyHandler')
            ->willReturn($handler->reveal());

        TypeHandlerService::addHandler(
            TestType::class, 'MyHandler'
        );

        $service = new TypeHandlerService($container->reveal());

        $instantiationMethods = $service->resolveTypes(['TestClass' => new TestType()]);

        $this->assertInternalType('array', $instantiationMethods);
        $this->assertContainsOnlyInstancesOf(InstantiationMethod::class, $instantiationMethods);

        /** @var InstantiationMethod $testMethod */
        $testMethod = current($instantiationMethods);

        $this->assertContains('Test', $testMethod->getMethodBody());
    }
}