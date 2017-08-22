<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\ClassBuilderService;
use Reinfi\OptimizedServiceManager\Service\MappingService;
use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Reinfi\OptimizedServiceManager\Service\TypeHandlerService;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Factory
 */
class OptimizerServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return OptimizerServiceInterface
     */
    public function __invoke(ContainerInterface $container): OptimizerServiceInterface
    {
        /** @var MappingService $mappingService */
        $mappingService = $container->get(MappingService::class);

        /** @var ClassBuilderService $classBuilderService */
        $classBuilderService = $container->get(ClassBuilderService::class);

        /** @var TypeHandlerService $typeHandlerService */
        $typeHandlerService = $container->get(TypeHandlerService::class);

        return new OptimizerService(
            $mappingService,
            $classBuilderService,
            $typeHandlerService
        );
    }
}