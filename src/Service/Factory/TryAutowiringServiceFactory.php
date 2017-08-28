<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Service\AutoWiring\ResolverService;
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
        /** @var ResolverService $resolverService */
        $resolverService = $container->get(ResolverService::class);

        return new TryAutowiringService(
            $container,
            $resolverService
        );
    }
}