<?php

namespace Reinfi\OptimizedServiceManager\Integration\Service\Handler;

use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\Handler\InvokableHandler;
use Reinfi\OptimizedServiceManager\Service\Service2;
use Reinfi\OptimizedServiceManager\Types\Invokable;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Service\Handler
 */
class InvokableHandlerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itReturnsMethodToInstantiateClass()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        /** @var InvokableHandler $handler */
        $handler = $serviceManager->get(InvokableHandler::class);

        $type = new Invokable(Service2::class);

        $method = $handler->handle($type);

        $this->assertEquals(
            Service2::class,
            $method->getClassName()
        );

        $this->assertCount(
            1,
            $method->getMethodBody(),
            'There should only be one method body part'
        );

        $this->assertContains(
            sprintf('return new \\%s();', $type->getService()),
            $method->getMethodBody()
        );
    }
}