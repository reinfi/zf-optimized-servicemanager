<?php

namespace Reinfi\OptimizedServiceManager\Integration\Service\Handler;

use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\Handler\DelegatorHandler;
use Reinfi\OptimizedServiceManager\Service\Service2;
use Reinfi\OptimizedServiceManager\Types\Delegator;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Service\Handler
 */
class DelegatorHandlerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itReturnsMethodToInstantiateClass()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        /** @var DelegatorHandler $handler */
        $handler = $serviceManager->get(DelegatorHandler::class);

        $type = new Delegator(Service2::class);

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
            sprintf(
                'return $this->container->get(\'%s\');',
                $type->getService()
            ),
            $method->getMethodBody()
        );
    }
}