<?php

namespace Reinfi\OptimizedServiceManager\Mvc\Service\Factory;

use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Mvc\Service\Factory
 */
class ServiceListenerFactory
{
    /**
     * @param ServiceManager $manager
     *
     * @return ServiceListener
     */
    public function __invoke(ServiceManager $manager): ServiceListener
    {
        return new ServiceListener($manager);
    }
}