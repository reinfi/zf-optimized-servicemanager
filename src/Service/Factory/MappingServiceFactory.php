<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Service\AutoWiringService;
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

        /** @var AutoWiringService $autowiringService */
        $autowiringService = $container->get(AutoWiringService::class);

        return new MappingService(
            $container,
            $config['service_manager'],
            $autowiringService
        );
    }
}