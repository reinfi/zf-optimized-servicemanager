<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Service\AutoWiring\ResolverService;
use Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler\Factory
 */
class AutowireHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return AutowireHandler
     */
    public function __invoke(ContainerInterface $container): AutowireHandler
    {
        /** @var ResolverService $resolverService */
        $resolverService = $container->get(ResolverService::class);

        return new AutowireHandler($resolverService, $container);
    }
}