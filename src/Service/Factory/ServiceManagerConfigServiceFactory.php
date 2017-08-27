<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\ServiceManagerConfigService;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Factory
 */
class ServiceManagerConfigServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ServiceManagerConfigService
     */
    public function __invoke(ContainerInterface $container): ServiceManagerConfigService
    {
        $config = $container->get('config');

        return new ServiceManagerConfigService(
            $config['service_manager']
        );
    }
}