<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\MappingService;
use Reinfi\OptimizedServiceManager\Service\TryAutowiringService;

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

        /** @var TryAutowiringService $autowiringService */
        $autowiringService = $container->get(TryAutowiringService::class);

        return new MappingService(
            $config['service_manager'],
            $autowiringService
        );
    }
}