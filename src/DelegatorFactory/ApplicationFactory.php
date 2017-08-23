<?php

namespace Reinfi\OptimizedServiceManager\DelegatorFactory;

use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Zend\Mvc\Application;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager as ZendServiceManager;

/**
 * @package Reinfi\DependencyInjection\Factory\Application
 */
class ApplicationFactory implements FactoryInterface, DelegatorFactoryInterface
{
    /**
     * Create the Application service (v3)
     *
     * Creates a Zend\Mvc\Application service, passing it the configuration
     * service and the service manager instance.
     *
     * @param  ZendServiceManager $container
     *
     * @return Application
     */
    public function __invoke(
        ZendServiceManager $container
    ) {
        $managerClass = OptimizerService::SERVICE_MANAGER_FQCN;

        return new Application(
            $container->get('config'),
            new $managerClass($container),
            $container->get('EventManager'),
            $container->get('Request'),
            $container->get('Response')
        );
    }

    /**
     * Create the Application service (v2)
     *
     * Proxies to __invoke().
     *
     * @param ServiceLocatorInterface|ZendServiceManager $container
     *
     * @return Application
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container);
    }

    /**
     * @param ServiceLocatorInterface|ZendServiceManager $serviceLocator
     * @param string                                     $name
     * @param string                                     $requestedName
     * @param callable                                   $callback
     *
     * @return Application
     */
    public function createDelegatorWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName,
        $callback
    ) {
        if (class_exists(OptimizerService::SERVICE_MANAGER_FQCN)) {
            return $this($serviceLocator);
        }

        return $callback();
    }
}