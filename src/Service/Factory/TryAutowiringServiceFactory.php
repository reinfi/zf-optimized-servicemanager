<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Service\AutoWiringService;
use Reinfi\OptimizedServiceManager\Service\TryAutowiringService;

/**
 * @package Reinfi\DependencyInjection\Service\Optimizer\Factory
 */
class TryAutowiringServiceFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return TryAutowiringService
     */
    public function __invoke(ContainerInterface $container): TryAutowiringService
    {
        /** @var AutoWiringService $autowiringService */
        $autowiringService = $container->get(AutoWiringService::class);

        return new TryAutowiringService(
            $container,
            $autowiringService
        );
    }
}