<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler\Factory;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler;
use Reinfi\OptimizedServiceManager\Service\Handler\ClosureHandler;
use Reinfi\OptimizedServiceManager\Service\Handler\InvokableHandler;
use Reinfi\OptimizedServiceManager\Service\TryAutowiringService;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler\Factory
 */
class ClosureHandlerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ClosureHandler
     */
    public function __invoke(ContainerInterface $container): ClosureHandler
    {
        /** @var TryAutowiringService $tryAutowiringService */
        $tryAutowiringService = $container->get(TryAutowiringService::class);

        /** @var InvokableHandler $invokableHandler */
        $invokableHandler = $container->get(InvokableHandler::class);

        /** @var AutowireHandler $autowireHandler */
        $autowireHandler = $container->get(AutowireHandler::class);

        return new ClosureHandler(
            $container,
            $tryAutowiringService,
            $invokableHandler,
            $autowireHandler
        );
    }
}