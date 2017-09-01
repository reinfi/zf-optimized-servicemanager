<?php

namespace Reinfi\OptimizedServiceManager\Mvc\Service\Factory;

use Psr\Container\ContainerInterface;
use Zend\EventManager\SharedEventManager;

/**
 * @package Reinfi\OptimizedServiceManager\Mvc\Service\Factory
 */
class SharedEventManagerFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return SharedEventManager
     */
    public function __invoke(ContainerInterface $container): SharedEventManager
    {
        return new SharedEventManager();
    }
}