<?php

namespace Reinfi\OptimizedServiceManager\DelegatorFactory;

use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Zend\Console\Console;
use Zend\Mvc\Application;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager as ZendServiceManager;
use Zend\ServiceManager\ServiceManager;

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

        /** @var ServiceManager $manager */
        if ($container->has($managerClass)) {
            $manager = $container->get($managerClass);
        } else {
            $manager = new $managerClass($container);
        }

        $application = new Application(
            $container->get('config'),
            $manager,
            $container->get('EventManager'),
            $container->get('Request'),
            $container->get('Response')
        );

        $manager
            ->setAllowOverride(true)
            ->setService(Application::class, $application)
            ->setAllowOverride(false);

        return $application;
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
        if (!Console::isConsole() && class_exists(OptimizerService::SERVICE_MANAGER_FQCN)) {
            return $this($serviceLocator);
        }

        return $callback();
    }
}