<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\TypeHandlerService;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Factory
 */
class TypeHandlerServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return TypeHandlerService
     */
    public function __invoke(ContainerInterface $container): TypeHandlerService
    {
        return new TypeHandlerService($container);
    }
}