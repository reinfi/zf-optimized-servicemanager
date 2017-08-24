<?php

namespace Reinfi\OptimizedServiceManager\Integration\Service\Handler;

use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\Handler\FactoryHandler;
use Reinfi\OptimizedServiceManager\Service\Service2;
use Reinfi\OptimizedServiceManager\Types\Factory;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Service\Handler
 */
class FactoryHandlerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itReturnsMethodToInstantiateClass()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../../resources/config.php'
        );

        /** @var FactoryHandler $handler */
        $handler = $serviceManager->get(FactoryHandler::class);

        $type = new Factory(Service2::class);

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
                'return parent::get(\'%s\');',
                $type->getService()
            ),
            $method->getMethodBody()
        );
    }
}