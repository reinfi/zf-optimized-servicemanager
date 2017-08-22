<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\MappingService;

/**
 * @package Reinfi\DependencyInjection\Service\Optimizer\Factory
 */
class MappingServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return MappingService
     */
    public function __invoke(ContainerInterface $container): MappingService
    {
        $config = $container->get('config');

        return new MappingService(
            $config['service_manager']
        );
    }
}