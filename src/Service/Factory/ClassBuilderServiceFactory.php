<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\ClassBuilderService;

/**
 * @package Reinfi\DependencyInjection\Service\Optimizer\Factory
 */
class ClassBuilderServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ClassBuilderService
     */
    public function __invoke(ContainerInterface $container): ClassBuilderService
    {
        $config = $container->get('config');

        return new ClassBuilderService(
            $config['service_manager']
        );
    }
}