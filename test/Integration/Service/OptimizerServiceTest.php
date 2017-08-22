<?php

namespace Reinfi\OptimizedServiceManager\Integration\Service;

use Nette\PhpGenerator\ClassType;
use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Reinfi\OptimizedServiceManager\Service\Service1;
use Reinfi\OptimizedServiceManager\Service\Service2;
use Reinfi\OptimizedServiceManager\Service\Service3;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Service
 */
class OptimizerServiceTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itReturnsGeneratedManagerClass()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../resources/config.php'
        );

        /** @var OptimizerService $service */
        $service = $serviceManager->get(OptimizerService::class);

        $namespace = $service->generate();

        $this->assertCount(1, $namespace->getClasses());
        $this->assertEquals(OptimizerService::SERVICE_MANAGER_NAMESPACE, $namespace->getName());

        /** @var ClassType $managerClass */
        $managerClass = current($namespace->getClasses());
        $this->assertEquals(
            OptimizerService::SERVICE_MANAGER_CLASSNAME,
            $managerClass->getName()
        );
    }

    /**
     * @test
     */
    public function itHasMappingOfServices()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../resources/config.php'
        );

        /** @var OptimizerService $service */
        $service = $serviceManager->get(OptimizerService::class);

        $namespace = $service->generate();

        $this->assertCount(1, $namespace->getClasses());
        $this->assertEquals(OptimizerService::SERVICE_MANAGER_NAMESPACE, $namespace->getName());

        /** @var ClassType $managerClass */
        $managerClass = current($namespace->getClasses());

        $mappingsProperty = $managerClass->getProperty('mappings');

        $mappings = $mappingsProperty->getValue();
        $this->assertInternalType('array', $mappings);
        $this->assertArrayHasKey(Service1::class, $mappings);
        $this->assertArrayHasKey(Service2::class, $mappings);
        $this->assertArrayHasKey(Service3::class, $mappings);
    }
}